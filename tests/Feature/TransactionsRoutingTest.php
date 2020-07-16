<?php

namespace Tests\Feature;

use App\Article;
use App\Customer;
use App\Kit;
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
                        'comments',
                        'user',
                        'customer',
                        'paymentMethod',
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

    public function testCreate(): void
    {
        $user = factory(User::class)->create();
        $payment_method = factory(PaymentMethod::class)->create([
            'parameters' => json_encode(['requires_account'])
        ]);
        $customer = factory(Customer::class)->create();

        $article_1 = factory(Article::class)->create();
        $article_1->prices()->save(factory(Price::class)->make(['value' => 4]));
        $article_2 = factory(Article::class)->create();
        $article_2->prices()->save(factory(Price::class)->make(['value' => 3.5]));
        $kit = factory(Kit::class)->create();
        $kit->prices()->save(factory(Price::class)->make(['value' => 3]));

        self::assertIsInt($kit->id);

        $response = $this->postJson('/api/transactions', [
            'value' => 11.5,
            'comments' => 'foo bar',
            'user_id' => $user->id,
            'payment_method_id' => $payment_method->id,
            'customer_id' => $customer->id,
            'articles' => [$article_1->id, $article_2->id, $article_2->id],
            'kits' => [$kit->id],
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'value',
                    'comments',
                    'user',
                    'customer',
                    'paymentMethod',
                    'items',
//                    'articles',
//                    'kits',
                ]
            ])
            ->assertJson([
                'data' => [
                    'value' => 11.5,
                    'comments' => 'foo bar',
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
                        'needsCashDrawer' => $payment_method->needs_cash_drawer,
                        'iconName' => $payment_method->icon_name,
                        'parameters' => json_encode(['requires_account']),
                    ],
                    'items' => [
                        [
                            'id' => $kit->id,
                            'type' => 'App\Kit',
                            'name' => $kit->name,
                            'quantity' => 1,
                        ],
                        [
                            'id' => $article_1->id,
                            'type' => 'App\Article',
                            'name' => $article_1->name,
                            'quantity' => 1,
                        ],
                        [
                            'id' => $article_2->id,
                            'type' => 'App\Article',
                            'name' => $article_2->name,
                            'quantity' => 2,
                        ],
                    ],
//                    'articles' => [
//                        [
//                            'id' => $article_1->id,
//                            'name' => $article_1->name,
//                            'quantity' => $article_1->quantity,
//                            'unitPrice' => $article_1->unit_price,
//                            'price' => [
//                                'id' => $article_1->price()->id,
//                                'value' => 4,
//                            ]
//                        ],
//                        [
//                            'id' => $article_2->id,
//                            'name' => $article_2->name,
//                            'quantity' => $article_2->quantity,
//                            'unitPrice' => $article_2->unit_price,
//                            'price' => [
//                                'id' => $article_2->price()->id,
//                                'value' => 3.50,
//                            ]
//                        ],
//                    ],
//                    'kits' => [
//                        [
//                            'id' => $kit->id,
//                            'name' => $kit->name,
//                            'quantity' => $kit->quantity,
//                            'price' => [
//                                'id' => $kit->price()->id,
//                                'value' => 3.00,
//                            ],
//                            'nbArticles' => 0,
//                        ],
//                    ],
                ]
            ]);
    }

    public function testUpdate1(): void
    {
        $user_1 = factory(User::class)->create();
        $user_2 = factory(User::class)->create();
        $payment_method_1 = factory(PaymentMethod::class)->create();
        $payment_method_2 = factory(PaymentMethod::class)->create([
            'parameters' => json_encode(['requires_account'])
        ]);
        $customer = factory(Customer::class)->create(['is_staff' => true]);

        $article_1 = factory(Article::class)->create();
        $article_1->prices()->save(factory(Price::class)->make(['value' => 4]));

        $transaction = factory(Transaction::class)->create([
            'user_id' => $user_1->id, 'payment_method_id' => $payment_method_1->id,
        ]);
        $transaction->articles()->attach($article_1->id, ['quantity' => 2]);

        $article_2 = factory(Article::class)->create();
        $article_2->prices()->save(factory(Price::class)->make(['value' => 3.5]));
        $kit = factory(Kit::class)->create();
        $kit->prices()->save(factory(Price::class)->make(['value' => 1.5]));

        $response = $this->putJson('/api/transactions/' . $transaction->id, [
            'value' => 8.5,
            'comments' => 'foo bar',
            'user_id' => $user_2->id,
            'payment_method_id' => $payment_method_2->id,
            'customer_id' => $customer->id,
            'articles' => [$article_2->id, $article_2->id],
            'kits' => [$kit->id],
            'detached_articles' => [$article_1->id],
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'value',
                    'comments',
                    'user',
                    'customer',
                    'paymentMethod',
                    'items',
//                    'articles',
//                    'kits',
                ]
            ])
            ->assertJson([
                'data' => [
                    'id' => $transaction->id,
                    'value' => 8.50,
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
                    ],
                    'paymentMethod' => [
                        'id' => $payment_method_2->id,
                        'name' => $payment_method_2->name,
                        'needsCashDrawer' => $payment_method_2->needs_cash_drawer,
                        'iconName' => $payment_method_2->icon_name,
                        'parameters' => json_encode(['requires_account']),
                    ],
                    'items' => [
                        [
                            'id' => $kit->id,
                            'type' => 'App\Kit',
                            'name' => $kit->name,
                            'quantity' => 1,
                        ],
                        [
                            'id' => $article_2->id,
                            'type' => 'App\Article',
                            'name' => $article_2->name,
                            'quantity' => 2,
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

        $article = factory(Article::class)->create();
        $article->prices()->save(factory(Price::class)->make(['value' => 4]));

        $transaction = factory(Transaction::class)->create([
            'user_id' => $user->id, 'payment_method_id' => $payment_method->id,
            'customer_id' => $customer->id,
        ]);
        $transaction->articles()->attach($article->id, ['quantity' => 2]);

        $response = $this->getJson('/api/transactions/' . $transaction->id);

        $response->assertStatus(200)
            ->assertJsonStructure(['data' => [
                'id',
                'comments',
                'user',
                'paymentMethod',
                'customer',
                'items',
//              'articles',
//              'kits',
            ]])
            ->assertJson([
                'data' => [
                    'id' => $transaction->id,
                    'value' => $transaction->value,
                    'comments' => $transaction->comments,
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
                        'needsCashDrawer' => $payment_method->needs_cash_drawer,
                        'iconName' => $payment_method->icon_name,
                        'parameters' => $payment_method->parameters,
                    ],
                    'items' => [
                        [
                            'id' => $article->id,
                            'type' => 'App\Article',
                            'name' => $article->name,
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

        $article = factory(Article::class)->create();
        $article->prices()->save(factory(Price::class)->make(['value' => 4]));

        $transaction = factory(Transaction::class)->create([
            'user_id' => $user->id, 'payment_method_id' => $payment_method->id,
            'customer_id' => $customer->id,
        ]);
        $transaction->articles()->attach($article->id, ['quantity' => 2]);

        self::assertEquals(1, TransactionDetail::whereTransactionId($transaction->id)->count());

        $response = $this->deleteJson('/api/transactions/' . $transaction->id);
        $response->assertStatus(204);
        // Check the 'ON DELETE CASCADE' behaviour of transaction_details
        self::assertEquals(0, TransactionDetail::whereTransactionId($transaction->id)->count());
    }
}
