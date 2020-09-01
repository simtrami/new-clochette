<?php

namespace Tests\Feature;

use App\Barrel;
use App\Bottle;
use App\Customer;
use App\Bundle;
use App\Food;
use App\Other;
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
    public function testIndex(): void
    {
        $user = factory(User::class)->create();
        $payment_method = factory(PaymentMethod::class)->create();
        $customer = factory(Customer::class)->create();

        factory(Transaction::class)->create([
            'user_id' => $user->id, 'payment_method_id' => $payment_method->id,
            'customer_id' => $customer->id,
        ]);
        factory(Transaction::class)->create([
            'user_id' => $user->id, 'payment_method_id' => $payment_method->id,
        ]);

        $response = $this->get('/api/transactions');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    0 => [
                        'id',
                        'value',
                        'comment',
                        'user',
                        'customer',
                        'paymentMethod',
                    ],
                    1 => [
                        'id',
                        'value',
                        'comment',
                        'user',
                        'paymentMethod',
                    ]
                ]
            ]);
    }

    public function testCreate(): void
    {
        $user = factory(User::class)->create();
        $payment_method = factory(PaymentMethod::class)->create(['debit_customer' => true]);
        $customer = factory(Customer::class)->create();

        $barrel = factory(Barrel::class)->create();
        $barrel->prices()->save(factory(Price::class)->make(['value' => 4]));
        $food = factory(Food::class)->create();
        $food->prices()->save(factory(Price::class)->make(['value' => 3.5]));
        $bundle = factory(Bundle::class)->create();
        $bundle->prices()->save(factory(Price::class)->make(['value' => 3]));

        self::assertIsInt($bundle->id);

        $response = $this->postJson('/api/transactions', [
            'value' => 11.5,
            'comment' => 'foo bar',
            'user_id' => $user->id,
            'payment_method_id' => $payment_method->id,
            'customer_id' => $customer->id,
            'barrels' => [
                [
                    'id' => $barrel->id,
                    'quantity' => 1,
                ],
            ],
            'food' => [
                [
                    'id' => $food->id,
                    'quantity' => 2,
                ],
            ],
            'bundles' => [
                [
                    'id' => $bundle->id,
                    'quantity' => 1,
                ],
            ],
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'value',
                    'comment',
                    'user',
                    'customer',
                    'paymentMethod',
                    'items',
                ]
            ])
            ->assertJson([
                'data' => [
                    'value' => 11.5,
                    'comment' => 'foo bar',
                    'user' => [
                        'id' => $user->id,
                        'name' => $user->name,
                        'username' => $user->username,
                        'email' => $user->email,
                        'roles' => [],
                        'permissions' => [],
                    ],
                    'customer' => [
                        'id' => $customer->id,
                        'firstName' => $customer->first_name,
                        'lastName' => $customer->last_name,
                        'nickname' => $customer->nickname,
                        'balance' => $customer->balance,
                        'isStaff' => $customer->is_staff,
                    ],
                    'paymentMethod' => [
                        'id' => $payment_method->id,
                        'name' => $payment_method->name,
                        'debitCustomer' => $payment_method->debit_customer,
                        'iconName' => $payment_method->icon_name,
                        'parameters' => null,
                    ],
                    'items' => [
                        [
                            'id' => $barrel->id,
                            'type' => 'App\Barrel',
                            'name' => $barrel->name,
                            'quantity' => 1,
                        ],
                        [
                            'id' => $food->id,
                            'type' => 'App\Food',
                            'name' => $food->name,
                            'quantity' => 2,
                        ],
                        [
                            'id' => $bundle->id,
                            'type' => 'App\Bundle',
                            'name' => $bundle->name,
                            'quantity' => 1,
                        ],
                    ],
                ]
            ]);
    }

    public function testUpdate1(): void
    {
        $user_1 = factory(User::class)->create();
        $user_2 = factory(User::class)->create();
        $payment_method_1 = factory(PaymentMethod::class)->create();
        $payment_method_2 = factory(PaymentMethod::class)->create(['debit_customer' => true]);
        $customer = factory(Customer::class)->create(['is_staff' => true]);

        $barrel = factory(Barrel::class)->create();
        $barrel->prices()->save(factory(Price::class)->make(['value' => 4]));
        $food = factory(Food::class)->create();
        $food->prices()->save(factory(Price::class)->make(['value' => 6]));

        $transaction = factory(Transaction::class)->create([
            'user_id' => $user_1->id, 'payment_method_id' => $payment_method_1->id,
        ]);
        $transaction->barrels()->attach($barrel->id, ['quantity' => 2]);
        $transaction->food()->attach($food->id, ['quantity' => 1]);

        $bottle = factory(Bottle::class)->create();
        $bottle->prices()->save(factory(Price::class)->make(['value' => 3.5]));
        $bundle = factory(Bundle::class)->create();
        $bundle->prices()->save(factory(Price::class)->make(['value' => 1.5]));

        $response = $this->putJson('/api/transactions/' . $transaction->id, [
            'value' => 8.5,
            'comment' => 'foo bar',
            'user_id' => $user_2->id,
            'payment_method_id' => $payment_method_2->id,
            'customer_id' => $customer->id,
            'barrels' => [
                [
                    'id' => $barrel->id,
                    'quantity' => 1,
                ],
            ],
            'bottles' => [
                [
                    'id' => $bottle->id,
                    'quantity' => 2,
                ],
            ],
            'bundles' => [
                [
                    'id' => $bundle->id,
                    'quantity' => 1,
                ],
            ],
            'detached_food' => [$food->id],
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'value',
                    'comment',
                    'user',
                    'customer',
                    'paymentMethod',
                    'items',
                ]
            ])
            ->assertJson([
                'data' => [
                    'id' => $transaction->id,
                    'value' => 8.50,
                    'comment' => 'foo bar',
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
                    ],
                    'paymentMethod' => [
                        'id' => $payment_method_2->id,
                        'name' => $payment_method_2->name,
                        'debitCustomer' => $payment_method_2->debit_customer,
                        'iconName' => $payment_method_2->icon_name,
                        'parameters' => null,
                    ],
                    'items' => [
                        [
                            'id' => $barrel->id,
                            'type' => 'App\Barrel',
                            'name' => $barrel->name,
                            'quantity' => 1,
                        ],
                        [
                            'id' => $bottle->id,
                            'type' => 'App\Bottle',
                            'name' => $bottle->name,
                            'quantity' => 2,
                        ],
                        [
                            'id' => $bundle->id,
                            'type' => 'App\Bundle',
                            'name' => $bundle->name,
                            'quantity' => 1,
                        ],
                    ],
                ]]);
    }

    public function testUpdate2(): void
    {
        $response = $this->putJson('/api/transactions/0');
        $response->assertStatus(404);
    }

    public function testShow1(): void
    {
        $user = factory(User::class)->create();
        $payment_method = factory(PaymentMethod::class)->create();
        $customer = factory(Customer::class)->create();

        $other = factory(Other::class)->create();
        $other->prices()->save(factory(Price::class)->make(['value' => 4]));

        $transaction = factory(Transaction::class)->create([
            'user_id' => $user->id, 'payment_method_id' => $payment_method->id,
            'customer_id' => $customer->id,
        ]);
        $transaction->others()->attach($other->id, ['quantity' => 2]);

        $response = $this->getJson('/api/transactions/' . $transaction->id);

        $response->assertStatus(200)
            ->assertJsonStructure(['data' => [
                'id',
                'comment',
                'user',
                'paymentMethod',
                'customer',
                'items',
//              'articles',
//              'bundles',
            ]])
            ->assertJson([
                'data' => [
                    'id' => $transaction->id,
                    'value' => $transaction->value,
                    'comment' => $transaction->comment,
                    'user' => [
                        'id' => $user->id,
                        'name' => $user->name,
                        'username' => $user->username,
                        'email' => $user->email,
                    ],
                    'customer' => [
                        'id' => $customer->id,
                        'firstName' => $customer->first_name,
                        'lastName' => $customer->last_name,
                        'nickname' => $customer->nickname,
                        'isStaff' => $customer->is_staff,
                    ],
                    'paymentMethod' => [
                        'id' => $payment_method->id,
                        'name' => $payment_method->name,
                        'debitCustomer' => $payment_method->debit_customer,
                        'iconName' => $payment_method->icon_name,
                        'parameters' => $payment_method->parameters,
                    ],
                    'items' => [
                        [
                            'id' => $other->id,
                            'type' => 'App\Other',
                            'name' => $other->name,
                            'quantity' => 2,
                        ],
                    ],
                ]]);
    }

    public function testShow2(): void
    {
        $response = $this->getJson('/api/transactions/0');
        $response->assertStatus(404);
    }

    public function testDestroy1(): void
    {
        $user = factory(User::class)->create();
        $payment_method = factory(PaymentMethod::class)->create();
        $customer = factory(Customer::class)->create();

        $transaction = factory(Transaction::class)->create([
            'user_id' => $user->id, 'payment_method_id' => $payment_method->id,
            'customer_id' => $customer->id,
        ]);

        $response = $this->deleteJson('/api/transactions/' . $transaction->id);
        $response->assertStatus(204);
        // Check whether the resource is unreachable
        $response = $this->deleteJson('/api/transactions/' . $transaction->id);
        $response->assertStatus(404);
        $response = $this->putJson('/api/transactions/' . $transaction->id);
        $response->assertStatus(404);
        $response = $this->getJson('/api/transactions/' . $transaction->id);
        $response->assertStatus(404);
        // Check the destruction in database
        self::assertNull(Transaction::find($transaction->id));
    }

    public function testDestroy2(): void
    {
        $user = factory(User::class)->create();
        $payment_method = factory(PaymentMethod::class)->create();
        $customer = factory(Customer::class)->create();

        $food = factory(Food::class)->create();
        $food->prices()->save(factory(Price::class)->make(['value' => 4]));

        $transaction = factory(Transaction::class)->create([
            'user_id' => $user->id, 'payment_method_id' => $payment_method->id,
            'customer_id' => $customer->id,
        ]);
        $transaction->food()->attach($food->id, ['quantity' => 2]);

        self::assertEquals(1, TransactionDetail::whereTransactionId($transaction->id)->count());

        $response = $this->deleteJson('/api/transactions/' . $transaction->id);
        $response->assertStatus(204);
        // Check the 'ON DELETE CASCADE' behaviour of transaction_details
        self::assertEquals(0, TransactionDetail::whereTransactionId($transaction->id)->count());
    }
}
