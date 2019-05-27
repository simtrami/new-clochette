<?php

namespace App\Http\Controllers\Api;

use App\Article;
use App\Bottle;
use App\Http\Controllers\Controller;
use App\Http\Resources\ArticleCollectionResource;
use App\Http\Resources\BottleResource;
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

class BottlesController extends Controller
{
    /**
     * @return AnonymousResourceCollection
     */
    public function index()
    {
        return ArticleCollectionResource::collection(Article::has('bottle')
            ->paginate(10));
    }

    /**
     * @param Bottle $bottle
     * @return BottleResource
     */
    public function show(Bottle $bottle)
    {
        $this->checkIfTrashed($bottle);
        return new BottleResource($bottle);
    }

    /**
     * @param Bottle $bottle
     */
    private function checkIfTrashed(Bottle $bottle)
    {
        if ($bottle->trashed() ||
            $bottle->article->trashed() ||
            $bottle->article->item->trashed()) {
            throw new NotFoundHttpException(
                "Requested resource does not exist or has been deleted.");
        }
    }

    /**
     * @param Request $request
     * @return BottleResource
     */
    public function store(Request $request)
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

        $bottle = new Bottle($data);
        $bottle->article()->associate(Article::find($item->id));
        $bottle->save();

        return new BottleResource($bottle);
    }

    /**
     * @param Bottle $bottle
     * @param Request $request
     * @return BottleResource
     */
    public function update(Bottle $bottle, Request $request)
    {
        $this->checkIfTrashed($bottle);

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

        $item = $bottle->article->item;
        // Update item's fields
        $item->update($data);

        // Update price / create a new one
        if ($request->has('value')) {
            $item->changePrice($data['value']);
        }

        $article = $bottle->article;
        // Update article's fields
        $article->update($data);

        // Update supplier
        if ($request->has('supplier_id')) {
            $article->supplier()
                ->associate(Supplier::find($data['supplier_id']));
            $article->update();
        }

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
        $this->checkIfTrashed($bottle);

        try {
            $bottle->article->item->delete();
            $bottle->article->delete();
            $bottle->delete();
        } catch (Exception $err) {
            return response()->json($err, 500);
        }

        return response(null, 204);
    }
}
