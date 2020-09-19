<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\BundleResource;
use App\Models\Bundle;
use App\Models\Price;
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
        return BundleResource::collection(Bundle::paginate(10));
    }

    /**
     * @param Bundle $bundle
     * @return BundleResource
     */
    public function show(Bundle $bundle): BundleResource
    {
        return new BundleResource($bundle->loadMissing('prices', 'articles'));
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
            'barrels' => 'array|min:1',
            'barrels.*.id' => 'required_with:barrels|exists:barrels',
            'barrels.*.quantity' => 'required_with:barrels|integer|min:1',
            'bottles' => 'array|min:1',
            'bottles.*.id' => 'required_with:bottles|exists:bottles',
            'bottles.*.quantity' => 'required_with:bottles|integer|min:1',
            'food' => 'array|min:1',
            'food.*.id' => 'required_with:food|exists:food',
            'food.*.quantity' => 'required_with:food|integer|min:1',
            'others' => 'array|min:1',
            'others.*.id' => 'required_with:others|exists:others',
            'others.*.quantity' => 'required_with:others|integer|min:1',
        ]);

        $bundle = Bundle::create($data);
        $bundle->setActivePrice(new Price($data));
        $this->syncItems($bundle, $data);
        $bundle->push();

        return new BundleResource($bundle->loadMissing('articles'));
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
            'barrels' => 'array|min:1',
            'barrels.*.id' => 'required_with:barrels|exists:barrels',
            'barrels.*.quantity' => 'required_with:barrels|integer|min:1',
            'bottles' => 'array|min:1',
            'bottles.*.id' => 'required_with:bottles|exists:bottles',
            'bottles.*.quantity' => 'required_with:bottles|integer|min:1',
            'food' => 'array|min:1',
            'food.*.id' => 'required_with:food|exists:food',
            'food.*.quantity' => 'required_with:food|integer|min:1',
            'others' => 'array|min:1',
            'others.*.id' => 'required_with:others|exists:others',
            'others.*.quantity' => 'required_with:others|integer|min:1',
            'detached_barrels' => 'array|min:1',
            'detached_barrels.*' => 'required_with:detached_barrels|exists:barrels,id',
            'detached_bottles' => 'array|min:1',
            'detached_bottles.*' => 'required_with:detached_bottles|exists:bottles,id',
            'detached_bundles' => 'array|min:1',
            'detached_bundles.*' => 'required_with:detached_bundles|exists:bundles,id',
            'detached_food' => 'array|min:1',
            'detached_food.*' => 'required_with:detached_food|exists:food,id',
            'detached_others' => 'array|min:1',
            'detached_others.*' => 'required_with:detached_others|exists:others,id',
        ]);

        $this->syncItems($bundle, $data);
        $this->detachItems($bundle, $data);

        // Update price / create a new one
        if ($request->has('value')) {
            $bundle->setActivePrice(new Price($data));
        }
        $bundle->update($data);

        return new BundleResource($bundle->loadMissing('prices', 'articles'));
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

    /**
     * @param Bundle $bundle
     * @param array $data
     */
    private function syncItems(Bundle $bundle, array $data): void
    {
        $reshape_data = static function ($data) {
            foreach ($data as $item) {
                $items[$item['id']] = ['quantity' => $item['quantity']];
            }
            return $items ?? [];
        };

        if (array_key_exists('barrels', $data)) {
            $bundle->barrels()->syncWithoutDetaching($reshape_data($data['barrels']));
        }
        if (array_key_exists('bottles', $data)) {
            $bundle->bottles()->syncWithoutDetaching($reshape_data($data['bottles']));
        }
        if (array_key_exists('food', $data)) {
            $bundle->food()->syncWithoutDetaching($reshape_data($data['food']));
        }
        if (array_key_exists('others', $data)) {
            $bundle->others()->syncWithoutDetaching($reshape_data($data['others']));
        }
    }

    /**
     * @param Bundle $bundle
     * @param array $data
     */
    private function detachItems(Bundle $bundle, array $data): void
    {
        if (array_key_exists('detached_barrels', $data)) {
            $bundle->barrels()->detach($data['detached_barrels']);
        }
        if (array_key_exists('detached_bottles', $data)) {
            $bundle->bottles()->detach($data['detached_bottles']);
        }
        if (array_key_exists('detached_food', $data)) {
            $bundle->food()->detach($data['detached_food']);
        }
        if (array_key_exists('detached_others', $data)) {
            $bundle->others()->detach($data['detached_others']);
        }
    }
}
