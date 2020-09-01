<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\TransactionResource;
use App\Transaction;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Routing\ResponseFactory;

class TransactionsController extends Controller
{
    /**
     * @return AnonymousResourceCollection
     */
    public function index(): AnonymousResourceCollection
    {
        return TransactionResource::collection(Transaction::paginate(10));
    }

    /**
     * @param Transaction $transaction
     * @return TransactionResource
     */
    public function show(Transaction $transaction): TransactionResource
    {
        return new TransactionResource($transaction->loadMissing('details'));
    }

    /**
     * @param Request $request
     * @return TransactionResource
     */
    public function store(Request $request): TransactionResource
    {
        $data = $request->validate([
            'value' => 'required|numeric',
            'comment' => 'string|min:2|max:255',
            'user_id' => 'required|exists:users,id',
            'payment_method_id' => 'required|exists:payment_methods,id',
            'customer_id' => 'required_if:payment_method_parameter,requires_account|exists:customers,id',
            'articles' => 'array|min:1',
            'articles.*' => 'required_with:articles|exists:articles,id',
            'bundles' => 'array|min:1',
            'bundles.*' => 'required_with:bundles|exists:bundles,id',
        ]);

        $transaction = new Transaction($data);
        $transaction->save();

        $this->addItems($transaction, $data);

        $transaction->push();
        return new TransactionResource($transaction
            ->loadMissing(['user', 'customer', 'paymentMethod', 'details']));
    }

    /**
     * @param Transaction $transaction
     * @param Request $request
     * @return TransactionResource
     */
    public function update(Transaction $transaction, Request $request): TransactionResource
    {
        $data = $request->validate([
            'value' => 'numeric',
            'comment' => 'string|min:2|max:255',
            'user_id' => 'exists:users,id',
            'payment_method_id' => 'exists:payment_methods,id',
            'customer_id' => 'exists:customers,id',
            'articles' => 'array|min:1',
            'articles.*' => 'required_with:articles|exists:articles,id',
            'bundles' => 'array|min:1',
            'bundles.*' => 'required_with:bundles|exists:bundles,id',
            'detached_articles' => 'array|min:1',
            'detached_articles.*' => 'required_with:detached_articles|exists:articles,id',
            'detached_bundles' => 'array|min:1',
            'detached_bundles.*' => 'required_with:detached_bundles|exists:bundles,id',
        ]);

        $this->detachItems($transaction, $data);
        $this->addItems($transaction, $data);

        // Update transaction's fields and saves the potentially new association(s)
        $transaction->update($data);
        $transaction->refresh();

        return new TransactionResource($transaction->loadMissing(['details', 'paymentMethod']));
    }

    /**
     * @param Transaction $transaction
     * @return ResponseFactory|JsonResponse|Response
     * @throws Exception
     */
    public function destroy(Transaction $transaction)
    {
        try {
            $transaction->delete();
        } catch (Exception $err) {
            return response()->json($err, 500);
        }

        return response(null, 204);
    }

    /**
     * @param Transaction $transaction
     * @param array $data
     */
    private function addItems(Transaction $transaction, array $data): void
    {
        if (array_key_exists('bundles', $data)) {
            $occurrences = array_count_values($data['bundles']);
            $bundles = array_unique($data['bundles']);
            foreach ($bundles as $bundleId) {
                $transaction->bundles()->attach($bundleId, ['quantity' => $occurrences[$bundleId]]);
            }
        }
        if (array_key_exists('articles', $data)) {
            $occurrences = array_count_values($data['articles']);
            $articles = array_unique($data['articles']);
            foreach ($articles as $articleId) {
                $transaction->articles()->attach($articleId, ['quantity' => $occurrences[$articleId]]);
            }
        }
    }

    /**
     * @param Transaction $transaction
     * @param array $data
     */
    private function detachItems(Transaction $transaction, array $data): void
    {
        if (array_key_exists('detached_bundles', $data)) {
            $transaction->bundles()->detach($data['detached_bundles']);
        }
        if (array_key_exists('detached_articles', $data)) {
            $transaction->articles()->detach($data['detached_articles']);
        }
    }
}
