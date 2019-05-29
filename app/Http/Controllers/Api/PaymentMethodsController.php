<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\PaymentMethodResource;
use App\PaymentMethod;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Routing\ResponseFactory;
use Illuminate\Validation\Rule;

class PaymentMethodsController extends Controller
{
    /**
     * @return AnonymousResourceCollection
     */
    public function index()
    {
        return PaymentMethodResource::collection(PaymentMethod::paginate(10));
    }

    /**
     * @param PaymentMethod $paymentMethod
     * @return PaymentMethodResource
     */
    public function show(PaymentMethod $paymentMethod)
    {
        return new PaymentMethodResource($paymentMethod);
    }

    /**
     * @param Request $request
     * @return PaymentMethodResource
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|min:2|max:255|unique:payment_methods',
            'needs_cash_drawer' => 'boolean',
            'icon_name' => 'string|min:2|max:100',
            'parameters' => 'array|min:1',
        ]);

        if ($request->has('parameters')) {
            $data['parameters'] = json_encode($data['parameters']);
        }
        $paymentMethod = new PaymentMethod($data);

        $paymentMethod->save();

        return new PaymentMethodResource($paymentMethod);
    }

    /**
     * @param PaymentMethod $paymentMethod
     * @param Request $request
     * @return PaymentMethodResource
     */
    public function update(PaymentMethod $paymentMethod, Request $request)
    {
        $data = $request->validate([
            'name' => [
                'string', 'min:2', 'max:255',
                Rule::unique('payment_methods')->ignore($paymentMethod),
            ],
            'needs_cash_drawer' => 'boolean',
            'icon_name' => 'string|min:2|max:100',
            'parameters' => 'array|min:1',
        ]);

        if ($request->has('parameters')) {
            $data['parameters'] = json_encode($data['parameters']);
        }

        $paymentMethod->update($data);

        return new PaymentMethodResource($paymentMethod);
    }

    /**
     * @param PaymentMethod $paymentMethod
     * @return ResponseFactory|JsonResponse|Response
     * @throws Exception
     */
    public function destroy(PaymentMethod $paymentMethod)
    {
        try {
            $paymentMethod->delete();
        } catch (Exception $err) {
            return response()->json($err, 500);
        }

        return response(null, 204);
    }
}
