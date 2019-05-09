<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\SupplierResource;
use App\Supplier;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Routing\ResponseFactory;
use Illuminate\Validation\Rule;


class SuppliersController extends Controller
{
    /**
     * @return AnonymousResourceCollection
     */
    public function index()
    {
        return SupplierResource::collection(Supplier::paginate(10));
    }

    /**
     * @param Supplier $supplier
     * @return SupplierResource
     */
    public function show(Supplier $supplier)
    {
        return new SupplierResource($supplier->loadMissing('contacts'));
    }

    /**
     * @param Request $request
     * @return SupplierResource
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|min:2|max:255|unique:suppliers',
            'description' => 'required|string|min:2|max:500',
            'address' => 'nullable|string',
            'phone' => 'nullable|string',
            'email' => 'nullable|email',
            'supplier_since' => 'required|date'
        ]);

        $supplier = new Supplier($data);

        $supplier->save();

        return new SupplierResource($supplier);
    }

    /**
     * @param Supplier $supplier
     * @param Request $request
     * @return SupplierResource
     */
    public function update(Supplier $supplier, Request $request)
    {
        $data = $request->validate([
            'name' => [
                'string', 'min:2', 'max:255',
                Rule::unique('suppliers')->ignore($supplier)
            ],
            'description' => 'string|min:2|max:500',
            'address' => 'nullable|string',
            'phone' => 'nullable|string',
            'email' => 'nullable|email',
            'supplier_since' => 'date'
        ]);

        $supplier->update($data);

        return new SupplierResource($supplier->loadMissing('contacts'));
    }

    /**
     * @param Supplier $supplier
     * @return ResponseFactory|JsonResponse|Response
     * @throws Exception
     */
    public function destroy(Supplier $supplier)
    {
        try {
            $supplier->delete();
        } catch (Exception $e) {
            return response()->json($e, 500);
        }

        return response(null, 204);
    }
}
