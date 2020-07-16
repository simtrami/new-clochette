<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\KitCollectionResource;
use App\Http\Resources\KitResource;
use App\Kit;
use App\Price;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Routing\ResponseFactory;

class KitsController extends Controller
{
    /**
     * @return AnonymousResourceCollection
     */
    public function index(): AnonymousResourceCollection
    {
        return KitCollectionResource::collection(Kit::paginate(10));
    }

    /**
     * @param Kit $kit
     * @return KitResource
     */
    public function show(Kit $kit): KitResource
    {
        return new KitResource($kit->loadMissing('articles'));
    }

    /**
     * @param Request $request
     * @return KitResource
     */
    public function store(Request $request): KitResource
    {
        $data = $request->validate([
            'name' => 'required|string|min:2|max:255',
            'quantity' => 'required|numeric|min:0',
            'value' => 'required|numeric|min:0',
        ]);

        $kit = Kit::create($data);
        $kit->prices()->save(new Price($data));
        $kit->push();

        return new KitResource($kit);
    }

    /**
     * @param Kit $kit
     * @param Request $request
     * @return KitResource
     */
    public function update(Kit $kit, Request $request): KitResource
    {
        $data = $request->validate([
            'name' => 'string|min:2|max:255',
            'quantity' => 'numeric|min:0',
            'value' => 'numeric|min:0',
            'articles' => 'array|min:1|bail',
            'articles.*.id' => 'required_with:articles|exists:articles,id|distinct',
            'articles.*.quantity' => 'required_with:articles|numeric|min:0',
            'detached_articles' => 'array|min:1',
            'detached_articles.*' => 'required_with:detached_articles|exists:articles,id',
        ]);

        $kit->update($data);
        // Update price / create a new one
        if ($request->has('value')) {
            $kit->changePrice($data['value']);
        }

        // Detach articles from the kit
        if ($request->has('detached_articles')) {
            $kit->articles()->detach($data['detached_articles']);
        }
        // Attach each articles to the kit with their respective quantities
        // Articles are not of the Article class but one of its children's class
        if ($request->has('articles')) {
            foreach ($data['articles'] as $selectedArticle) {
                $kit->articles()
                    ->syncWithoutDetaching([
                        $selectedArticle['id'] => [
                            'article_quantity' => $selectedArticle['quantity']
                        ]
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
        try {
            $kit->delete();
        } catch (Exception $err) {
            return response()->json($err, 500);
        }

        return response(null, 204);
    }
}
