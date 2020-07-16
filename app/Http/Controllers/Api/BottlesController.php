<?php

namespace App\Http\Controllers\Api;

use App\Article;
use App\Bottle;
use App\Http\Controllers\Controller;
use App\Http\Resources\ArticleCollectionResource;
use App\Http\Resources\BottleResource;
use App\Price;
use App\Supplier;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Routing\ResponseFactory;

class BottlesController extends Controller
{
    /**
     * @return AnonymousResourceCollection
     */
    public function index(): AnonymousResourceCollection
    {
        return ArticleCollectionResource::collection(Article::has('bottle')
            ->paginate(10));
    }

    /**
     * @param Bottle $bottle
     * @return BottleResource
     */
    public function show(Bottle $bottle): BottleResource
    {
        return new BottleResource($bottle);
    }

    /**
     * @param Request $request
     * @return BottleResource
     */
    public function store(Request $request): BottleResource
    {
        $data = $request->validate([
            'name' => 'required|string|min:2|max:255',
            'quantity' => 'required|numeric|min:0',
            'supplier_id' => 'exists:suppliers,id',
            'unit_price' => 'required|numeric|min:0',
            'value' => 'required|numeric|min:0',
            'volume' => 'required|numeric|min:0',
            'is_returnable' => 'boolean',
            'abv' => 'nullable|numeric|min:0',
            'ibu' => 'nullable|numeric|min:0',
            'variety' => 'nullable|string|min:1|max:255',
        ]);

        $article = Article::create($data);
        $article->prices()->save(new Price($data));
        if (array_key_exists('supplier_id', $data)) {
            $article->supplier()->associate(Supplier::find($data['supplier_id']));
        }
        $bottle = new Bottle($data);
        $bottle->article()->associate($article);
        $bottle->push();

        return new BottleResource($bottle);
    }

    /**
     * @param Bottle $bottle
     * @param Request $request
     * @return BottleResource
     */
    public function update(Bottle $bottle, Request $request): BottleResource
    {
        $data = $request->validate([
            'name' => 'string|min:2|max:255',
            'quantity' => 'numeric|min:0',
            'supplier_id' => 'exists:suppliers,id',
            'unit_price' => 'numeric|min:0',
            'value' => 'numeric|min:0',
            'volume' => 'numeric|min:0',
            'is_returnable' => 'boolean',
            'abv' => 'nullable|numeric|min:0',
            'ibu' => 'nullable|numeric|min:0',
            'variety' => 'nullable|string|min:1|max:255',
        ]);

        $article = $bottle->article;

        // Update supplier
        if (array_key_exists('supplier_id', $data)) {
            $article->supplier()->associate(Supplier::find($data['supplier_id']));
        }
        // Update price / create a new one
        if (array_key_exists('value', $data)) {
            $article->changePrice($data['value']);
        }
        $article->update($data);

        // Update bottle's fields
        $bottle->update($data);

        return new BottleResource($bottle);
    }

    /**
     * @param Bottle $bottle
     * @return ResponseFactory|JsonResponse|Response
     * @throws Exception
     */
    public function destroy(Bottle $bottle)
    {
        try {
            $bottle->delete();
        } catch (Exception $err) {
            return response()->json($err, 500);
        }

        return response(null, 204);
    }
}
