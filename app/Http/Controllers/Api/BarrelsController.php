<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\BarrelResource;
use App\Models\Barrel;
use App\Models\Price;
use App\Models\Supplier;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Routing\ResponseFactory;

class BarrelsController extends Controller
{
    /**
     * @return AnonymousResourceCollection
     */
    public function index(): AnonymousResourceCollection
    {
        return BarrelResource::collection(Barrel::paginate(10));
    }

    /**
     * @param Barrel $barrel
     * @return BarrelResource
     */
    public function show(Barrel $barrel): BarrelResource
    {
        return new BarrelResource($barrel->loadMissing('supplier', 'prices'));
    }

    /**
     * @param Request $request
     * @return BarrelResource
     */
    public function store(Request $request): BarrelResource
    {
        $data = $request->validate([
            'name' => 'required|string|min:2|max:255',
            'quantity' => 'required|numeric|min:0',
            'supplier_id' => 'exists:suppliers,id',
            'unit_price' => 'required|numeric|min:0',
            'value' => 'required|numeric|min:0',
            'second_value' => 'nullable|numeric|min:0',
            'volume' => 'required|numeric|min:0',
            'coupler' => 'nullable|string|min:1|max:255',
            'abv' => 'nullable|numeric|min:0|max:99.9',
            'ibu' => 'nullable|numeric|min:0|max:999.9',
        ]);

        $barrel = Barrel::create($data);
        $barrel->setActivePrice(new Price($data));
        if ($request->has('supplier_id')) {
            $barrel->supplier()->associate(Supplier::find($data['supplier_id']));
        }
        $barrel->push();

        return new BarrelResource($barrel);
    }

    /**
     * @param Barrel $barrel
     * @param Request $request
     * @return BarrelResource
     */
    public function update(Barrel $barrel, Request $request): BarrelResource
    {
        $data = $request->validate([
            'name' => 'string|min:2|max:255',
            'quantity' => 'numeric|min:0',
            'supplier_id' => 'exists:suppliers,id',
            'unit_price' => 'numeric|min:0',
            'second_value' => 'nullable|numeric|min:0',
            'value' => 'required_with:second_value|numeric|min:0',
            'volume' => 'numeric|min:0',
            'coupler' => 'nullable|string|min:1|max:255',
            'abv' => 'nullable|numeric|min:0|max:99.9',
            'ibu' => 'nullable|numeric|min:0|max:999.9',
        ]);

        // Update supplier
        if ($request->has('supplier_id')) {
            $barrel->supplier()->associate(Supplier::find($data['supplier_id']));
        }
        // Update price / create a new one
        if ($request->hasAny('value', 'second_value')) {
            $barrel->setActivePrice(new Price($data));
        }
        // Update barrel's fields
        $barrel->update($data);

        return new BarrelResource($barrel->loadMissing('supplier', 'prices'));
    }

    /**
     * @param Barrel $barrel
     * @return ResponseFactory|JsonResponse|Response
     * @throws Exception
     */
    public function destroy(Barrel $barrel)
    {
        try {
            $barrel->delete();
        } catch (Exception $err) {
            return response()->json($err, 500);
        }

        return response(null, 204);
    }
}
