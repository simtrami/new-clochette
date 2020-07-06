<?php

namespace App\Http\Controllers\Api;

use App\Article;
use App\Http\Controllers\Controller;
use App\Http\Resources\ArticleCollectionResource;
use App\Http\Resources\OtherResource;
use App\Item;
use App\Other;
use App\Price;
use App\Supplier;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Routing\ResponseFactory;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

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
        $this->checkIfTrashed($other);
        return new OtherResource($other);
    }

    /**
     * @param Other $other
     */
    private function checkIfTrashed(Other $other): void
    {
        if ($other->trashed() ||
            $other->article->trashed() ||
            $other->article->item->trashed()) {
            throw new NotFoundHttpException(
                "Requested resource does not exist or has been deleted.");
        }
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

        $other = new Other($data);
        $other->article()->associate(Article::find($item->id));
        $other->save();

        return new OtherResource($other);
    }

    /**
     * @param Other $other
     * @param Request $request
     * @return OtherResource
     */
    public function update(Other $other, Request $request): OtherResource
    {
        $this->checkIfTrashed($other);

        $data = $request->validate([
            'name' => 'string|min:2|max:255',
            'quantity' => 'numeric|min:0',
            'supplier_id' => 'exists:suppliers,id',
            'unit_price' => 'numeric|min:0',
            'value' => 'numeric|min:0',
            'description' => 'string|min:10|max:500',
        ]);

        $item = $other->article->item;
        // Update item's fields
        $item->update($data);

        // Update price / create a new one
        if ($request->has('value')) {
            $item->changePrice($data['value']);
        }

        $article = $other->article;
        // Update article's fields
        $article->update($data);

        // Update supplier
        if ($request->has('supplier_id')) {
            $article->supplier()
                ->associate(Supplier::find($data['supplier_id']));
            $article->update();
        }

        // Update other's fields
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
        $this->checkIfTrashed($other);

        try {
            $other->article->item->delete();
            $other->article->delete();
            $other->delete();
        } catch (Exception $err) {
            return response()->json($err, 500);
        }

        return response(null, 204);
    }
}
