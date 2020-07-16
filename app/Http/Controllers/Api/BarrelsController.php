<?php

namespace App\Http\Controllers\Api;

use App\Article;
use App\Barrel;
use App\Http\Controllers\Controller;
use App\Http\Resources\ArticleCollectionResource;
use App\Http\Resources\BarrelResource;
use App\Price;
use App\Supplier;
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
        return ArticleCollectionResource::collection(Article::has('barrel')
            ->paginate(10));
    }

    /**
     * @param Barrel $barrel
     * @return BarrelResource
     */
    public function show(Barrel $barrel): BarrelResource
    {
        return new BarrelResource($barrel);
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
            'abv' => 'nullable|numeric|min:0',
            'ibu' => 'nullable|numeric|min:0',
            'variety' => 'nullable|string|min:1|max:255',
        ]);

        $article = Article::create($data);
        $article->prices()->save(new Price($data));
        if ($request->has('supplier_id')) {
            $article->supplier()
                ->associate(Supplier::find($data['supplier_id']));
        }
        $barrel = new Barrel($data);
        $barrel->article()->associate($article);
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
            'value' => 'numeric|min:0',
            'second_value' => 'nullable|numeric|min:0',
            'volume' => 'numeric|min:0',
            'coupler' => 'nullable|string|min:1|max:255',
            'abv' => 'nullable|numeric|min:0',
            'ibu' => 'nullable|numeric|min:0',
            'variety' => 'nullable|string|min:1|max:255',
        ]);

        $article = $barrel->article;
        // Update article's fields
        $article->update($data);

        // Update price / create a new one
        $this->updatePrice($barrel, $request, $data);

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
        try {
            $barrel->delete();
        } catch (Exception $err) {
            return response()->json($err, 500);
        }

        return response(null, 204);
    }

    /**
     * @param Barrel $barrel
     * @param Request $request
     * @param array $data
     */
    private function updatePrice(Barrel $barrel, Request $request, array $data): void
    {
        if ($request->has(['value', 'second_value'])) {
            $barrel->changePrices($data['value'], $data['second_value']);
        } elseif ($request->has('value')) {
            $barrel->changePrice($data['value']);
        } elseif ($request->has('second_value')) {
            $barrel->changeSecondPrice($data['second_value']);
        }
    }
}
