<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\BundleCollectionResource;
use App\Http\Resources\BundleResource;
use App\Bundle;
use App\Price;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Routing\ResponseFactory;

class BundlesController extends Controller
{
    /**
     * @return AnonymousResourceCollection
     */
    public function index(): AnonymousResourceCollection
    {
        return BundleCollectionResource::collection(Bundle::paginate(10));
    }

    /**
     * @param Bundle $bundle
     * @return BundleResource
     */
    public function show(Bundle $bundle): BundleResource
    {
        return new BundleResource($bundle->loadMissing('articles'));
    }

    /**
     * @param Request $request
     * @return BundleResource
     */
    public function store(Request $request): BundleResource
    {
        $data = $request->validate([
            'name' => 'required|string|min:2|max:255',
            'quantity' => 'required|numeric|min:0',
            'value' => 'required|numeric|min:0',
        ]);

        $bundle = Bundle::create($data);
        $bundle->prices()->save(new Price($data));
        $bundle->push();

        return new BundleResource($bundle);
    }

    /**
     * @param Bundle $bundle
     * @param Request $request
     * @return BundleResource
     */
    public function update(Bundle $bundle, Request $request): BundleResource
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

        $bundle->update($data);
        // Update price / create a new one
        if ($request->has('value')) {
            $bundle->changePrice($data['value']);
        }

        // Detach articles from the bundle
        if ($request->has('detached_articles')) {
            $bundle->articles()->detach($data['detached_articles']);
        }
        // Attach each articles to the bundle with their respective quantities
        // Articles are not of the Article class but one of its children's class
        if ($request->has('articles')) {
            foreach ($data['articles'] as $selectedArticle) {
                $bundle->articles()
                    ->syncWithoutDetaching([
                        $selectedArticle['id'] => [
                            'article_quantity' => $selectedArticle['quantity']
                        ]
                    ]);
            }
        }

        return new BundleResource($bundle);
    }

    /**
     * @param Bundle $bundle
     * @return ResponseFactory|JsonResponse|Response
     * @throws Exception
     */
    public function destroy(Bundle $bundle)
    {
        try {
            $bundle->delete();
        } catch (Exception $err) {
            return response()->json($err, 500);
        }

        return response(null, 204);
    }
}
