<?php

namespace App\Http\Controllers\Api;

use App\Article;
use App\Http\Controllers\Controller;
use App\Http\Resources\ArticleCollectionResource;
use App\Http\Resources\OtherResource;
use App\Other;
use App\Price;
use App\Supplier;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Routing\ResponseFactory;

class OthersController extends Controller
{
    /**
     * @return AnonymousResourceCollection
     */
    public function index(): AnonymousResourceCollection
    {
        return ArticleCollectionResource::collection(Article::has('other')
            ->paginate(10));
    }

    /**
     * @param Other $other
     * @return OtherResource
     */
    public function show(Other $other): OtherResource
    {
        return new OtherResource($other);
    }

    /**
     * @param Request $request
     * @return OtherResource
     */
    public function store(Request $request): OtherResource
    {
        $data = $request->validate([
            'name' => 'required|string|min:2|max:255',
            'quantity' => 'required|numeric|min:0',
            'supplier_id' => 'exists:suppliers,id',
            'unit_price' => 'required|numeric|min:0',
            'value' => 'required|numeric|min:0',
            'description' => 'required|string|min:10|max:500',
        ]);

        $article = Article::create($data);
        $article->prices()->save(new Price($data));
        if ($request->has('supplier_id')) {
            $article->supplier()
                ->associate(Supplier::find($data['supplier_id']));
        }

        $other = new Other($data);
        $other->article()->associate($article);
        $other->push();

        return new OtherResource($other);
    }

    /**
     * @param Other $other
     * @param Request $request
     * @return OtherResource
     */
    public function update(Other $other, Request $request): OtherResource
    {
        $data = $request->validate([
            'name' => 'string|min:2|max:255',
            'quantity' => 'numeric|min:0',
            'supplier_id' => 'exists:suppliers,id',
            'unit_price' => 'numeric|min:0',
            'value' => 'numeric|min:0',
            'description' => 'string|min:10|max:500',
        ]);

        $article = $other->article;
        // Update article's fields
        $article->update($data);

        // Update price / create a new one
        if ($request->has('value')) {
            $article->changePrice($data['value']);
        }

        // Update supplier
        if ($request->has('supplier_id')) {
            $article->supplier()
                ->associate(Supplier::find($data['supplier_id']));
            $article->update();
        }

        // Update food's fields
        $other->update($data);

        return new OtherResource($other);
    }

    /**
     * @param Other $other
     * @return ResponseFactory|JsonResponse|Response
     * @throws Exception
     */
    public function destroy(Other $other)
    {
        try {
            $other->delete();
        } catch (Exception $err) {
            return response()->json($err, 500);
        }

        return response(null, 204);
    }
}
