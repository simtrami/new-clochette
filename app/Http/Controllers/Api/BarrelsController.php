<?php

namespace App\Http\Controllers\Api;

use App\Article;
use App\Barrel;
use App\Http\Controllers\Controller;
use App\Http\Resources\ArticleResource;
use App\Http\Resources\BarrelResource;
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

class BarrelsController extends Controller
{
    /**
     * @return AnonymousResourceCollection
     */
    public function index()
    {
        return ArticleResource::collection(Article::has('barrel')->paginate(10));
    }

    /**
     * @param Barrel $barrel
     * @return BarrelResource
     */
    public function show(Barrel $barrel)
    {
        if ($barrel->article->trashed()) {
            throw new NotFoundHttpException("Requested resource does not exist or has been deleted.");
        }
        return new BarrelResource($barrel);
    }

    /**
     * @param Request $request
     * @return BarrelResource
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|min:2|max:255',
            'quantity' => 'required|numeric|min:0',
            'supplier_id' => 'exists:suppliers,id',
            'unit_price' => 'required|numeric|min:0',
            'value' => 'required|numeric|min:0',
            'second_value' => 'nullable|numeric|min:0',
            'volume' => 'required|numeric|min:0',
            'withdrawal_type' => 'required|string|min:1|max:255'
        ]);

        $barrel = new Barrel($data);

        $article = new Article($data);
        if ($request->has('supplier_id')) {
            $article->supplier()->associate(Supplier::find($data['supplier_id']));
        }

        $item = new Item($data);
        $item->save();
        $item->prices()->save(new Price($data));

        $article->item()->associate(Item::find($item->id));
        $article->save();

        $barrel->article()->associate(Article::find($item->id));
        $barrel->save();

        return new BarrelResource($barrel);
    }

    /**
     * @param Barrel $barrel
     * @param Request $request
     * @return BarrelResource
     */
    public function update(Barrel $barrel, Request $request)
    {
        if ($barrel->article->trashed()) {
            throw new NotFoundHttpException("Requested resource does not exist or has been deleted.");
        }

        $data = $request->validate([
            'name' => 'string|min:2|max:255',
            'quantity' => 'numeric|min:0',
            'supplier_id' => 'exists:suppliers,id',
            'unit_price' => 'numeric|min:0',
            'value' => 'numeric|min:0',
            'second_value' => 'nullable|numeric|min:0',
            'volume' => 'numeric|min:0',
            'withdrawal_type' => 'string|min:1|max:255'
        ]);

        $item = $barrel->article->item;
        // Update item's fields
        $item->update($data);

        // Update price / create a new one
        if ($request->has(['value', 'second_value'])) {
            $item->changePrices($data['value'], $data['second_value']);
        } elseif ($request->has('value')) {
            $item->changePrice($data['value']);
        } elseif ($request->has('second_value')) {
            $item->changeSecondPrice($data['second_value']);
        }

        $article = $barrel->article;
        // Update article's fields
        $article->update($data);

        // Update supplier
        if ($request->has('supplier_id')) {
            $article->supplier()->associate(Supplier::find($data['supplier_id']));
            $article->update();
        }

        // Update barrel's fields
        $barrel->update($data);

        return new BarrelResource($barrel);
    }

    /**
     * @param Barrel $barrel
     * @return ResponseFactory|JsonResponse|Response
     * @throws Exception
     */
    public function destroy(Barrel $barrel)
    {
        if ($barrel->trashed() && $barrel->article->trashed() && $barrel->article->item->trashed()) {
            throw new NotFoundHttpException("Requested resource does not exist or has been deleted.");
        }

        try {
            $barrel->article->item->delete();
            $barrel->article->delete();
            $barrel->delete();
        } catch (Exception $err) {
            return response()->json($err, 500);
        }

        return response(null, 204);
    }
}