<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\KitCollectionResource;
use App\Http\Resources\KitResource;
use App\Item;
use App\Kit;
use App\Price;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Routing\ResponseFactory;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class KitsController extends Controller
{
    /**
     * @return AnonymousResourceCollection
     */
    public function index()
    {
        return KitCollectionResource::collection(Kit::paginate(10));
    }

    /**
     * @param Kit $kit
     * @return KitResource
     */
    public function show(Kit $kit)
    {
        $this->checkIfTrashed($kit);
        return new KitResource($kit->loadMissing('articles'));
    }

    /**
     * @param Kit $kit
     */
    private function checkIfTrashed(Kit $kit)
    {
        if ($kit->trashed() || $kit->item->trashed()) {
            throw new NotFoundHttpException(
                "Requested resource does not exist or has been deleted.");
        }
    }

    /**
     * @param Request $request
     * @return KitResource
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|min:2|max:255',
            'quantity' => 'required|numeric|min:0',
            'value' => 'required|numeric|min:0',
        ]);

        $item = Item::create($data);
        $item->prices()->save(new Price($data));

        $kit = new Kit;
        $kit->item()->associate($item);
        $kit->save();

        return new KitResource($kit);
    }

    /**
     * @param Kit $kit
     * @param Request $request
     * @return KitResource
     */
    public function update(Kit $kit, Request $request)
    {
        $this->checkIfTrashed($kit);

        $data = $request->validate([
            'name' => 'string|min:2|max:255',
            'quantity' => 'numeric|min:0',
            'value' => 'numeric|min:0',
            'articles' => 'array|min:1|bail',
            'articles.*.article_id' => 'required_with:articles|exists:articles,item_id|distinct',
            'articles.*.quantity' => 'required_with:articles|numeric|min:0',
            'detached_articles' => 'array|min:1',
            'detached_articles.*' => 'required_with:detached_articles|exists:articles,item_id',
        ]);

        $item = $kit->item;
        // Update item's fields
        $item->update($data);
        // Update price / create a new one
        if ($request->has('value')) {
            $item->changePrice($data['value']);
        }

        // Detach articles from the kit
        if ($request->has('detached_articles')) {
            $kit->articles()->detach($data['detached_articles']);
        }
        // Attach each articles to the kit with their respective quantities
        // Articles are not of the Article type but one of its child types:
        // articles' id is named article_id, not item_id
        if ($request->has('articles')) {
            foreach ($data['articles'] as $selectedArticle) {
                $kit->articles()
                    ->syncWithoutDetaching([
                        $selectedArticle['article_id'] =>
                            ['article_quantity' => $selectedArticle['quantity']]
                    ]);
            }
        }

        return new KitResource($kit);
    }

    /**
     * @param Kit $kit
     * @return ResponseFactory|JsonResponse|Response
     * @throws Exception
     */
    public function destroy(Kit $kit)
    {
        $this->checkIfTrashed($kit);

        try {
            $kit->item->delete();
            $kit->delete();
        } catch (Exception $err) {
            return response()->json($err, 500);
        }

        return response(null, 204);
    }
}
