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
use Illuminate\Database\Eloquent\Factories\Sequence;
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
        $customer = Customer::factory()->create();

        Transaction::factory()
            ->count(2)
            ->state(new Sequence(
                ['customer_id' => null],
                ['customer_id' => $customer->id],
            ))
            ->create();

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

    public function testCreate1(): void
    {
        $user = User::factory()->create();
        $payment_method = PaymentMethod::factory()->create(['debit_customer' => true]);
        $customer = Customer::factory()->create();

        $barrel = Barrel::factory()
            ->hasPrices(1, ['value' => 4])
            ->create();
        $food = Food::factory()
            ->hasPrices(1, ['value' => 3.5])
            ->create();
        $bundle = Bundle::factory()
            ->hasPrices(1, ['value' => 3])
            ->create();

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
                    'second_price' => false,
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
                            'id' => $food->id,
                            'type' => 'App\Food',
                            'name' => $food->name,
                            'quantity' => 2,
                            'value' => 3.5
                        ],
                        [
                            'id' => $bundle->id,
                            'type' => 'App\Bundle',
                            'name' => $bundle->name,
                            'quantity' => 1,
                            'value' => 3,
                        ],
                        [
                            'id' => $barrel->id,
                            'type' => 'App\Barrel',
                            'name' => $barrel->name,
                            'quantity' => 1,
                            'value' => 4,
                        ],
                    ],
                ]
            ]);
    }

    public function testCreate2(): void
    {
        $user = User::factory()->create();
        $payment_method = PaymentMethod::factory()->create(['debit_customer' => true]);
        $customer = Customer::factory()->create();

        $barrel = Barrel::factory()
            ->hasPrices(1, ['value' => 4])
            ->create();
        $food = Food::factory()
            ->hasPrices(1, ['value' => 3.5])
            ->create();
        $bundle = Bundle::factory()
            ->hasPrices(1, ['value' => 3])
            ->create();

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
                    'second_price' => true,
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

        // There must be a validation error (422) as the barrel does not have a second price value
        // although the 'second_price' request parameter is set to true.
        $response->assertStatus(422);
    }

    public function testCreate3(): void
    {
        $user = User::factory()->create();
        $payment_method = PaymentMethod::factory()->create(['debit_customer' => true]);
        $customer = Customer::factory()->create();

        $barrel = Barrel::factory()
            ->hasPrices(1, ['value' => 4, 'second_value' => 2])
            ->create();

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
                    'second_price' => true,
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
                            'value' => 2,
                        ],
                    ],
                ]
            ]);
    }

    public function testUpdate1(): void
    {
        $newUser = User::factory()->create();
        $newPaymentMethod = PaymentMethod::factory()->create(['debit_customer' => true]);
        $customer = Customer::factory()->create(['is_staff' => true]);

        $barrel = Barrel::factory()
            ->hasPrices(1, ['value' => 4, 'second_value' => 2])
            ->create();
        $food = Food::factory()
            ->hasPrices(1, ['value' => 6])
            ->create();

        $transaction = Transaction::factory()->create();
        $transaction->barrels()->attach($barrel->id, [
            'quantity' => 2,
            'value' => 4,
        ]);
        $transaction->food()->attach($food->id, [
            'quantity' => 1,
            'value' => 6,
        ]);

        $bottle = Bottle::factory()
            ->hasPrices(1, ['value' => 3.5])
            ->create();
        $bundle = Bundle::factory()
            ->hasPrices(1, ['value' => 1.5])
            ->create();

        $response = $this->putJson('/api/transactions/' . $transaction->id, [
            'value' => 8.5,
            'comment' => 'foo bar',
            'user_id' => $newUser->id,
            'payment_method_id' => $newPaymentMethod->id,
            'customer_id' => $customer->id,
            'barrels' => [
                [
                    'id' => $barrel->id,
                    'quantity' => 1,
                    'second_price' => true,
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
                        'id' => $newUser->id,
                        'name' => $newUser->name,
                        'username' => $newUser->username,
                        'email' => $newUser->email,
                    ],
                    'customer' => [
                        'id' => $customer->id,
                        'firstName' => $customer->first_name,
                        'lastName' => $customer->last_name,
                        'nickname' => $customer->nickname,
                        'isStaff' => 1,
                    ],
                    'paymentMethod' => [
                        'id' => $newPaymentMethod->id,
                        'name' => $newPaymentMethod->name,
                        'debitCustomer' => $newPaymentMethod->debit_customer,
                        'iconName' => $newPaymentMethod->icon_name,
                        'parameters' => null,
                    ],
                    'items' => [
                        [
                            'id' => $barrel->id,
                            'type' => 'App\Barrel',
                            'name' => $barrel->name,
                            'quantity' => 1,
                            'value' => 2,
                        ],
                        [
                            'id' => $bottle->id,
                            'type' => 'App\Bottle',
                            'name' => $bottle->name,
                            'quantity' => 2,
                            'value' => 3.5,
                        ],
                        [
                            'id' => $bundle->id,
                            'type' => 'App\Bundle',
                            'name' => $bundle->name,
                            'quantity' => 1,
                            'value' => 1.5,
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
        $user = User::factory()->create();
        $payment_method = PaymentMethod::factory()->create();
        $customer = Customer::factory()->create();

        $other = Other::factory()
            ->hasPrices(1, ['value' => 4])
            ->create();

        $transaction = Transaction::factory()
            ->state([
                'user_id' => $user->id,
                'payment_method_id' => $payment_method->id,
                'customer_id' => $customer->id,
            ])
            ->create();
        $transaction->others()->attach($other->id, [
            'quantity' => 2,
            'value' => 4,
        ]);

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
                            'value' => 4,
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
        $customer = Customer::factory()->create();

        $transaction = Transaction::factory()->create([
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
        $customer = Customer::factory()->create();

        $transaction = Transaction::factory()->create([
            'customer_id' => $customer->id,
        ]);

        $food = Food::factory()
            ->hasPrices(1, ['value' => 4])
            ->create();

        $transaction = Transaction::factory()->create([
            'customer_id' => $customer->id,
        ]);
        $transaction->food()->attach($food->id, ['quantity' => 2, 'value' => 4]);

        self::assertEquals(1, TransactionDetail::whereTransactionId($transaction->id)->count());

        $response = $this->deleteJson('/api/transactions/' . $transaction->id);
        $response->assertStatus(204);
        // Check the 'ON DELETE CASCADE' behaviour of transaction_details
        self::assertEquals(0, TransactionDetail::whereTransactionId($transaction->id)->count());
    }
}
