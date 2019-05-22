<?php

namespace App\Http\Controllers\Api;

use App\Article;
use App\Food;
use App\Http\Controllers\Controller;
use App\Http\Resources\ArticleResource;
use App\Http\Resources\FoodResource;
use App\Item;
use App\Price;
use App\Supplier;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Routing\ResponseFactory;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class FoodsController extends Controller
{
    /**
     * @return AnonymousResourceCollection
     */
    public function index()
    {
        return ArticleResource::collection(Article::has('food')
            ->paginate(10));
    }

    /**
     * @param Food $food
     * @return FoodResource
     */
    public function show(Food $food)
    {
        $this->checkIfTrashed($food);
        return new FoodResource($food);
    }

    /**
     * @param Food $food
     */
    private function checkIfTrashed(Food $food)
    {
        if ($food->trashed() ||
            $food->article->trashed() ||
            $food->article->item->trashed()) {
            throw new NotFoundHttpException(
                "Requested resource does not exist or has been deleted.");
        }
    }

    /**
     * @param Request $request
     * @return FoodResource
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|min:2|max:255',
            'quantity' => 'required|numeric|min:0',
            'supplier_id' => 'exists:suppliers,id',
            'unit_price' => 'required|numeric|min:0',
            'value' => 'required|numeric|min:0',
            'is_bulk' => 'boolean',
            'units_left' => 'required_if:is_bulk,true|numeric|min:0',
        ]);

        $item = new Item($data);
        $item->save();
        $item->prices()->save(new Price($data));

        $article = new Article($data);
        if ($request->has('supplier_id')) {
            $article->supplier()
                ->associate(Supplier::find($data['supplier_id']));
        }
        $article->item()->associate(Item::find($item->id));
        $article->save();

        $food = new Food($data);
        $food->article()->associate(Article::find($item->id));
        $food->save();

        return new FoodResource($food);
    }

    /**
     * @param Food $food
     * @param Request $request
     * @return FoodResource
     */
    public function update(Food $food, Request $request)
    {
        $this->checkIfTrashed($food);

        $data = $request->validate([
            'name' => 'string|min:2|max:255',
            'quantity' => 'numeric|min:0',
            'supplier_id' => 'exists:suppliers,id',
            'unit_price' => 'numeric|min:0',
            'value' => 'numeric|min:0',
            'is_bulk' => 'boolean',
            'units_left' => 'required_with:is_bulk|numeric|min:0',
        ]);

        $item = $food->article->item;
        // Update item's fields
        $item->update($data);

        // Update price / create a new one
        if ($request->has('value')) {
            $item->changePrice($data['value']);
        }

        $article = $food->article;
        // Update article's fields
        $article->update($data);

        // Update supplier
        if ($request->has('supplier_id')) {
            $article->supplier()
                ->associate(Supplier::find($data['supplier_id']));
            $article->update();
        }

        // Update food's fields
        $food->update($data);

        return new FoodResource($food);
    }

    /**
     * @param Food $food
     * @return ResponseFactory|JsonResponse|Response
     * @throws Exception
     */
    public function destroy(Food $food)
    {
        $this->checkIfTrashed($food);

        try {
            $food->article->item->delete();
            $food->article->delete();
            $food->delete();
        } catch (Exception $err) {
            return response()->json($err, 500);
        }

        return response(null, 204);
    }
}
