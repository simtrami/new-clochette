<?php

namespace Tests\Feature;

use App\Customer;
use App\Item;
use App\PaymentMethod;
use App\Price;
use App\Transaction;
use App\TransactionDetail;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TransactionsRoutingTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @return void
     */
    public function testIndex()
    {
        $user = factory(User::class)->create();
        $payment_method = factory(PaymentMethod::class)->create();
        $customer = factory(Customer::class)->create();

        factory(Transaction::class)->create([
            'user_id' => $user->id, 'payment_method_id' => $payment_method->id,
            'customer_id' => $customer->id
        ]);
        factory(Transaction::class)->create([
            'user_id' => $user->id, 'payment_method_id' => $payment_method->id
        ]);

        $response = $this->get('/api/transactions');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    0 => [
                        'id',
                        'value',
                        'comments',
                        'user',
                        'paymentMethod',
                        'customer',
                    ],
                    1 => [
                        'id',
                        'value',
                        'comments',
                        'user',
                        'paymentMethod',
                    ]
                ]
            ]);
    }

    public function testCreate()
    {
        $user = factory(User::class)->create();
        $payment_method = factory(PaymentMethod::class)->create([
            'parameters' => json_encode(['requires_account' => true])
        ]);
        $customer = factory(Customer::class)->create();

        $item_1 = factory(Item::class)->create();
        factory(Price::class)->create(['item_id' => $item_1->id, 'value' => 4]);
        $item_2 = factory(Item::class)->create();
        factory(Price::class)->create(['item_id' => $item_2->id, 'value' => 3.5]);

        $response = $this->postJson('/api/transactions', [
            'value' => 11.5,
            'comments' => 'foo bar',
            'user' => $user->id,
            'payment_method' => $payment_method->id,
            'payment_method_parameter' => 'requires_account',
            'customer' => $customer->id,
            'items' => [$item_1->id, $item_2->id, $item_1->id],
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'value',
                    'comments',
                    'user',
                    'paymentMethod',
                    'customer',
                    'items' => [0, 1],
                ]
            ]);
    }

    public function testUpdate()
    {
        $user_1 = factory(User::class)->create();
        $user_2 = factory(User::class)->create();
        $payment_method_1 = factory(PaymentMethod::class)->create();
        $payment_method_2 = factory(PaymentMethod::class)->create([
            'parameters' => json_encode(['requires_account' => true])
        ]);
        $customer = factory(Customer::class)->create(['is_staff' => true]);

        $item_1 = factory(Item::class)->create();
        factory(Price::class)->create(['item_id' => $item_1->id, 'value' => 4]);

        $transaction = factory(Transaction::class)->create([
            'user_id' => $user_1->id, 'payment_method_id' => $payment_method_1->id
        ]);
        factory(TransactionDetail::class)->create([
            'item_id' => $item_1->id, 'quantity' => 2, 'transaction_id' => $transaction->id,
        ]);

        $item_2 = factory(Item::class)->create();
        factory(Price::class)->create(['item_id' => $item_2->id, 'value' => 3.5]);
        $item_3 = factory(Item::class)->create();
        factory(Price::class)->create(['item_id' => $item_3->id, 'value' => 1.5]);

        $response = $this->putJson('/api/transactions/' . $transaction->id, [
            'value' => 8.5,
            'comments' => 'foo bar',
            'user' => $user_2->id,
            'payment_method' => $payment_method_2->id,
            'payment_method_parameter' => 'requires_account',
            'customer' => $customer->id,
            'items' => [$item_2->id, $item_3->id, $item_2->id],
            'detached_items' => [$item_1->id],
        ]);

        $response->assertStatus(200)
            ->assertJson(['data' => [
                'id' => $transaction->id,
                'comments' => 'foo bar',
                'user' => [
                    'id' => $user_2->id,
                    'name' => $user_2->name,
                    'username' => $user_2->username,
                    'email' => $user_2->email,
                ],
                'customer' => [
                    'id' => $customer->id,
                    'firstName' => $customer->first_name,
                    'lastName' => $customer->last_name,
                    'nickname' => $customer->nickname,
                    'isStaff' => 1,
                    'staffNickname' => $customer->staff_nickname,
                ],
                'paymentMethod' => [
                    'id' => $payment_method_2->id,
                    'name' => $payment_method_2->name,
                    'needsCashDrawer' => $payment_method_2->needs_cash_drawer ? 1 : 0,
                    'iconName' => 'attach_money',
                    'parameters' => '{"requires_account":true}',
                ],
                'items' => [
                    [
                        'itemId' => $item_2->id,
                        'itemName' => $item_2->name,
                        'quantity' => 2,
                    ],
                    [
                        'itemId' => $item_3->id,
                        'itemName' => $item_3->name,
                        'quantity' => 1,
                    ]
                ],
            ]]);
    }

    public function testShow()
    {
        $user = factory(User::class)->create();
        $payment_method = factory(PaymentMethod::class)->create();
        $customer = factory(Customer::class)->create();

        $item_1 = factory(Item::class)->create();
        factory(Price::class)->create(['item_id' => $item_1->id, 'value' => 4]);

        $transaction = factory(Transaction::class)->create([
            'user_id' => $user->id, 'payment_method_id' => $payment_method->id,
            'customer_id' => $customer->id,
        ]);
        factory(TransactionDetail::class)->create([
            'item_id' => $item_1->id, 'quantity' => 2, 'transaction_id' => $transaction->id,
        ]);

        $response = $this->getJson('/api/transactions/' . $transaction->id);

        $response->assertStatus(200)
            ->assertJsonStructure(['data' => [
                'id',
                'comments',
                'user',
                'paymentMethod',
                'customer',
                'items' => [
                    ['itemId', 'itemName', 'quantity'],
                ],
            ]]);
    }

    public function testDestroy()
    {
        $transaction = factory(Transaction::class)->create();

        $response = $this->deleteJson('/api/transactions/' . $transaction->id);
        $response->assertStatus(204);

        $response = $this->getJson('/api/transactions/' . $transaction->id);
        $response->assertStatus(404);
    }
}
