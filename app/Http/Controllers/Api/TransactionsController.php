<?php

namespace App\Http\Controllers\Api;

use App\Customer;
use App\Http\Controllers\Controller;
use App\Http\Resources\TransactionResource;
use App\Item;
use App\PaymentMethod;
use App\Transaction;
use App\TransactionDetail;
use App\User;
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
        return TransactionResource::collection(Transaction::with('customer')->paginate(10));
    }

    /**
     * @param Transaction $transaction
     * @return TransactionResource
     */
    public function show(Transaction $transaction): TransactionResource
    {
        return new TransactionResource($transaction->loadMissing('customer'));
    }

    /**
     * @param Request $request
     * @return TransactionResource
     */
    public function store(Request $request): TransactionResource
    {
        $data = $request->validate([
            'value' => 'required|numeric',
            'comments' => 'string|min:2|max:255',
            'user' => 'required|exists:users,id',
            'payment_method' => 'required|exists:payment_methods,id',
            'payment_method_parameter' => 'string|min:2',
            'customer' => 'required_if:payment_method_parameter,requires_account|exists:customers,id',
            'items' => 'required|array|min:1',
            'items.*' => 'required_with:items|exists:items,id',
        ]);

        $transaction = new Transaction($data);
        $transaction->save();

        $transaction->user()->associate(User::find($data['user']));
        $transaction->paymentMethod()->associate(PaymentMethod::find($data['payment_method']));

        if ($request->has('customer')) {
            $transaction->customer()->associate(Customer::find($data['customer']));
        }

        $occurrences = array_count_values($data['items']);
        $filteredItems = array_unique($data['items']);
        foreach ($filteredItems as $itemId) {
            $detail = new TransactionDetail(['quantity' => $occurrences[$itemId]]);
            $detail->item()->associate(Item::find($itemId));
            $transaction->details()->save($detail);
        }

        $transaction->save();

        return new TransactionResource($transaction->loadMissing('details'));
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
            'comments' => 'string|min:2|max:255',
            'user' => 'exists:users,id',
            'payment_method' => 'exists:payment_methods,id',
            'payment_method_parameter' => 'string|min:2',
            'customer' => 'exists:customers,id',
            'items' => 'array|min:1',
            'items.*' => 'required_with:items|exists:items,id',
            'detached_items' => 'array|min:1',
            'detached_items.*' => 'required_with:detached_items|exists:items,id',
        ]);

        $transaction->update($data);

        if ($request->has('user')) {
            $transaction->user()->associate(User::find($data['user']));
        }
        if ($request->has('payment_method')) {
            $transaction->paymentMethod()->associate(PaymentMethod::find($data['payment_method']));
        }
        if ($request->has('customer')) {
            $transaction->customer()->associate(Customer::find($data['customer']));
        }
        if ($request->has('items')) {
            $occurrences = array_count_values($data['items']);
            $filteredItems = array_unique($data['items']);
            foreach ($filteredItems as $itemId) {
                $detail = new TransactionDetail(['quantity' => $occurrences[$itemId]]);
                $detail->item()->associate(Item::find($itemId));
                $transaction->details()->save($detail);
            }
        }
        if ($request->has('detached_items')) {
            $transaction->items()->detach($data['detached_items']);
        }

        $transaction->save();

        return new TransactionResource(Transaction::find($transaction->id)->loadMissing('customer'));
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
}
