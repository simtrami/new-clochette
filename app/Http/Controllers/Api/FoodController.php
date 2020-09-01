<?php

namespace App\Http\Controllers\Api;

use App\Article;
use App\Food;
use App\Http\Controllers\Controller;
use App\Http\Resources\ArticleCollectionResource;
use App\Http\Resources\FoodResource;
use App\Price;
use App\Supplier;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Routing\ResponseFactory;

class FoodController extends Controller
{
    /**
     * @return AnonymousResourceCollection
     */
    public function index(): AnonymousResourceCollection
    {
        return FoodResource::collection(Food::paginate(10));
    }

    /**
     * @param Food $food
     * @return FoodResource
     */
    public function show(Food $food): FoodResource
    {
        return new FoodResource($food->loadMissing('supplier', 'prices'));
    }

    /**
     * @param Request $request
     * @return FoodResource
     */
    public function store(Request $request): FoodResource
    {
        $data = $request->validate([
            'name' => 'required|string|min:2|max:255',
            'quantity' => 'required|numeric|min:0',
            'supplier_id' => 'exists:suppliers,id',
            'unit_price' => 'required|numeric|min:0',
            'value' => 'required|numeric|min:0',
            'is_bulk' => 'boolean',
        ]);

        $food = Food::create($data);
        $food->prices()->save(new Price($data));
        if ($request->has('supplier_id')) {
            $food->supplier()->associate(Supplier::find($data['supplier_id']));
        }
        $food->push();

        return new FoodResource($food);
    }

    /**
     * @param Food $food
     * @param Request $request
     * @return FoodResource
     */
    public function update(Food $food, Request $request): FoodResource
    {
        $data = $request->validate([
            'name' => 'string|min:2|max:255',
            'quantity' => 'numeric|min:0',
            'supplier_id' => 'exists:suppliers,id',
            'unit_price' => 'numeric|min:0',
            'value' => 'numeric|min:0',
            'is_bulk' => 'boolean',
        ]);

        // Update supplier
        if ($request->has('supplier_id')) {
            $food->supplier()->associate(Supplier::find($data['supplier_id']));
        }
        // Update price / create a new one
        if ($request->has('value')) {
            $food->changePrice($data['value']);
        }
        // Update food's fields
        $food->update($data);

        return new FoodResource($food->loadMissing('supplier', 'prices'));
    }

    /**
     * @param Food $food
     * @return ResponseFactory|JsonResponse|Response
     * @throws Exception
     */
    public function destroy(Food $food)
    {
        try {
            $food->delete();
        } catch (Exception $err) {
            return response()->json($err, 500);
        }

        return response(null, 204);
    }
}
