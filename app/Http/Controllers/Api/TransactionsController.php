<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\TransactionResource;
use App\Models\Barrel;
use App\Models\Bottle;
use App\Models\Bundle;
use App\Models\Food;
use App\Models\Other;
use App\Models\PaymentMethod;
use App\Models\Transaction;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Routing\ResponseFactory;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

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
     * @throws ValidationException
     */
    public function store(Request $request): TransactionResource
    {
        $data = $request->validate([
            'value' => 'required|numeric',
            'comment' => 'string|min:2|max:255',
            'user_id' => 'required|exists:users,id',
            'payment_method_id' => 'required|exists:payment_methods,id',
            'customer_id' => [
                'exists:customers,id',
                Rule::requiredIf(function () use ($request) {
                    return PaymentMethod::findOrFail($request->get('payment_method_id'))->debit_customer;
                }),
            ],
            'barrels' => 'array|min:1',
            'barrels.*.id' => 'required_with:barrels|exists:barrels',
            'barrels.*.quantity' => 'required_with:barrels|integer|min:1',
            'barrels.*.second_price' => 'required_with:barrels|boolean',
            'bottles' => 'array|min:1',
            'bottles.*.id' => 'required_with:bottles|exists:bottles',
            'bottles.*.quantity' => 'required_with:bottles|integer|min:1', // TODO: add max = qty in stock
            'bundles' => 'array|min:1',
            'bundles.*.id' => 'required_with:bundles|exists:bundles',
            'bundles.*.quantity' => 'required_with:bundles|integer|min:1', // TODO: add max = qty in stock
            'food' => 'array|min:1',
            'food.*.id' => 'required_with:food|exists:food',
            'food.*.quantity' => 'required_with:food|integer|min:1', // TODO: add max = qty in stock
            'others' => 'array|min:1',
            'others.*.id' => 'required_with:others|exists:others',
            'others.*.quantity' => 'required_with:others|integer|min:1', // TODO: add max = qty in stock
        ]);

        $transaction = Transaction::create($data);

        $this->syncItems($transaction, $data);
        $transaction->push();

        return new TransactionResource($transaction
            ->loadMissing(['user', 'customer', 'paymentMethod', 'details']));
    }

    /**
     * @param Transaction $transaction
     * @param Request $request
     * @return TransactionResource
     * @throws ValidationException
     */
    public function update(Transaction $transaction, Request $request): TransactionResource
    {
        $data = $request->validate([
            'value' => 'numeric',
            'comment' => 'string|min:2|max:255',
            'user_id' => 'exists:users,id',
            'payment_method_id' => 'exists:payment_methods,id',
            'customer_id' => [
                'exists:customers,id',
                Rule::requiredIf(function () use ($request, $transaction) {
                    if ($request->has('payment_method_id')) {
                        return PaymentMethod::findOrFail($request->get('payment_method_id'))->debit_customer;
                    }
                    return $transaction->paymentMethod->debit_customer;
                }),
            ],
            'barrels' => 'array|min:1',
            'barrels.*.id' => 'required_with:barrels|exists:barrels',
            'barrels.*.quantity' => 'required_with:barrels|integer|min:1',
            'barrels.*.second_price' => 'required_with:barrels|boolean',
            'bottles' => 'array|min:1',
            'bottles.*.id' => 'required_with:bottles|exists:bottles',
            'bottles.*.quantity' => 'required_with:bottles|integer|min:1', // TODO: add max = qty in stock
            'bundles' => 'array|min:1',
            'bundles.*.id' => 'required_with:bundles|exists:bundles',
            'bundles.*.quantity' => 'required_with:bundles|integer|min:1', // TODO: add max = qty in stock
            'food' => 'array|min:1',
            'food.*.id' => 'required_with:food|exists:food',
            'food.*.quantity' => 'required_with:food|integer|min:1', // TODO: add max = qty in stock
            'others' => 'array|min:1',
            'others.*.id' => 'required_with:others|exists:others',
            'others.*.quantity' => 'required_with:others|integer|min:1', // TODO: add max = qty in stock
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

        $this->syncItems($transaction, $data);
        $this->detachItems($transaction, $data);

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
     * @throws ValidationException
     */
    private function syncItems(Transaction $transaction, array $data): void
    {
        if (array_key_exists('bottles', $data)) {
            $transaction->bottles()->syncWithoutDetaching($this->reshapeData($data['bottles'], Bottle::class));
        }
        if (array_key_exists('food', $data)) {
            $transaction->food()->syncWithoutDetaching($this->reshapeData($data['food'], Food::class));
        }
        if (array_key_exists('others', $data)) {
            $transaction->others()->syncWithoutDetaching($this->reshapeData($data['others'], Other::class));
        }
        if (array_key_exists('bundles', $data)) {
            $transaction->bundles()->syncWithoutDetaching($this->reshapeData($data['bundles'], Bundle::class));
        }

        if (array_key_exists('barrels', $data)) {
            // Barrels can have a price with a second value, custom logic is mandatory.
            $transaction->barrels()->syncWithoutDetaching($this->reshapeBarrelsData($data['barrels']));
        }
    }

    /**
     * Reshape function for passing the items array to the syncWithoutDetaching method
     *
     * @param $items
     * @param $type
     * @return array
     */
    private function reshapeData($items, $type): array
    {
        foreach ($items as $item) {
            $reshapedItems[$item['id']] = [
                'quantity' => $item['quantity'],
                'value' => $type::findOrFail($item['id'])->activePrice()->value,
            ];
        }
        return $reshapedItems ?? [];
    }

    /**
     * Reshape function for passing the barrels array to the syncWithoutDetaching method
     * Specific to the barrels as it is the only item type to have two possible values for its price
     *
     * @param array $barrels
     * @return array
     * @throws ValidationException
     * @throws Exception
     */
    private function reshapeBarrelsData(array $barrels): array
    {
        foreach ($barrels as $barrel) {
            $price = Barrel::findOrFail($barrel['id'])->activePrice();
            if (!$price) {
                throw new Exception("Barrel {$barrel['id']} doesn't have an active price.");
            }
            // If no second_value is registered on the active price and the second_price request parameter
            // is set to true, there will be a validation error.
            if ((bool)$barrel['second_price'] === false) {
                $reshapedBarrels[$barrel['id']] = [
                    'quantity' => $barrel['quantity'],
                    'value' => Barrel::findOrFail($barrel['id'])->activePrice()->value,
                ];
            } elseif ((bool)$barrel['second_price'] === true && $price->second_value !== null) {
                $reshapedBarrels[$barrel['id']] = [
                    'quantity' => $barrel['quantity'],
                    'value' => Barrel::findOrFail($barrel['id'])->activePrice()->second_value,
                ];
            } else {
                throw ValidationException::withMessages([
                    'barrels' => "Barrel {$barrel['id']} doesn't have a second price value.",
                ]);
            }
        }
        return $reshapedBarrels ?? [];
    }

    /**
     * @param Transaction $transaction
     * @param array $data
     */
    private function detachItems(Transaction $transaction, array $data): void
    {
        if (array_key_exists('detached_barrels', $data)) {
            $transaction->barrels()->detach($data['detached_barrels']);
        }
        if (array_key_exists('detached_bottles', $data)) {
            $transaction->bottles()->detach($data['detached_bottles']);
        }
        if (array_key_exists('detached_food', $data)) {
            $transaction->food()->detach($data['detached_food']);
        }
        if (array_key_exists('detached_others', $data)) {
            $transaction->others()->detach($data['detached_others']);
        }
        if (array_key_exists('detached_bundles', $data)) {
            $transaction->bundles()->detach($data['detached_bundles']);
        }
    }
}
