<?php

namespace App\Http\Controllers\Api;

use App\Customer;
use App\Http\Controllers\Controller;
use App\Http\Resources\CustomerResource;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Routing\ResponseFactory;
use Illuminate\Validation\Rule;

class CustomersController extends Controller
{
    /**
     * @return AnonymousResourceCollection
     */
    public function index()
    {
        return CustomerResource::collection(Customer::paginate(10));
    }

    /**
     * @param Customer $customer
     * @return CustomerResource
     */
    public function show(Customer $customer)
    {
        return new CustomerResource($customer);
    }

    /**
     * @param Request $request
     * @return CustomerResource
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'nickname' => 'required|string|min:2|max:100|unique:customers',
            'balance' => 'required|numeric',
            'is_staff' => 'boolean',
            'staff_nickname' => 'nullable|string|min:2|max:100|unique:customers'
        ]);

        $customer = new Customer($data);

        $customer->save();

        return new CustomerResource($customer);
    }

    /**
     * @param Customer $customer
     * @param Request $request
     * @return CustomerResource
     */
    public function update(Customer $customer, Request $request)
    {
        $data = $request->validate([
            'first_name' => 'string',
            'last_name' => 'string',
            'nickname' => [
                'string', 'min:2', 'max:100',
                Rule::unique('customers')->ignore($customer)
            ],
            'balance' => 'numeric',
            'is_staff' => 'boolean',
            'staff_nickname' => [
                'nullable', 'string', 'min:2', 'max:100',
                Rule::unique('customers')->ignore($customer)
            ],
        ]);

        $customer->update($data);

        return new CustomerResource($customer);
    }

    /**
     * @param Customer $customer
     * @return ResponseFactory|JsonResponse|Response
     * @throws Exception
     */
    public function destroy(Customer $customer)
    {
        try {
            $customer->delete();
        } catch (Exception $e) {
            return response()->json($e, 500);
        }

        return response(null, 204);
    }
}