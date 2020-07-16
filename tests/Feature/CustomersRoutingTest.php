<?php

namespace Tests\Feature;

use App\Customer;
use App\Transaction;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CustomersRoutingTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @return void
     */
    public function testIndex(): void
    {
        factory(Customer::class, 2)->create();

        $response = $this->get('/api/customers');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    0 => [
                        'id',
                        'firstName',
                        'lastName',
                        'nickname',
                        'balance',
                        'isStaff'
                    ],
                    1 => [
                        'id',
                        'firstName',
                        'lastName',
                        'nickname',
                        'balance',
                        'isStaff'
                    ]
                ]
            ]);
    }

    public function testCreate(): void
    {
        $response = $this->postJson('/api/customers', [
            'first_name' => 'Customer',
            'last_name' => 'McTest',
            'nickname' => 'customer.mctest',
            'balance' => 10.2,
            'is_staff' => true
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'firstName',
                    'lastName',
                    'nickname',
                    'balance',
                    'isStaff',
                ]
            ]);
    }

    public function testUpdate1(): void
    {
        $customer = factory(Customer::class)->create();

        $response = $this->putJson('/api/customers/' . $customer->id, [
            'first_name' => 'Customer',
            'last_name' => 'McTest',
            'nickname' => 'customer.mctest',
            'balance' => 0,
            'is_staff' => 1
        ]);

        $response->assertStatus(200)
            ->assertJson(['data' => [
                'id' => $customer->id,
                'firstName' => 'Customer',
                'lastName' => 'McTest',
                'nickname' => 'customer.mctest',
                'balance' => 0,
                'isStaff' => 1
            ]]);
    }

    public function testUpdate2(): void
    {
        $response = $this->putJson('/api/customers/0');
        $response->assertStatus(404);
    }

    public function testShow1(): void
    {
        $customer = factory(Customer::class)->create();

        $response = $this->getJson('/api/customers/' . $customer->id);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'firstName',
                    'lastName',
                    'nickname',
                    'balance',
                    'isStaff'
                ]
            ]);
    }

    public function testShow2(): void
    {
        $response = $this->getJson('/api/customers/0');
        $response->assertStatus(404);
    }

    public function testDestroy1(): void
    {
        $customer = factory(Customer::class)->create();

        $response = $this->deleteJson('/api/customers/' . $customer->id);
        $response->assertStatus(204);
        // Check whether the resource is unreachable
        $response = $this->deleteJson('/api/customers/' . $customer->id);
        $response->assertStatus(404);
        $response = $this->putJson('/api/customers/' . $customer->id);
        $response->assertStatus(404);
        $response = $this->getJson('/api/customers/' . $customer->id);
        $response->assertStatus(404);
        // Check the destruction in database
        self::assertNull(Customer::find($customer->id));
    }

    public function testDestroy2(): void
    {
        $customer = factory(Customer::class)->create();
        $user = factory(User::class)->create(['customer_id' => $customer->id]);

        self::assertEquals(1, User::whereCustomerId($customer->id)->count());

        $response = $this->deleteJson('/api/customers/' . $customer->id);
        $response->assertStatus(204);
        // Check the 'ON DELETE SET NULL' behaviour of users' customer_id
        self::assertEquals(0, User::whereCustomerId($customer->id)->count());
        self::assertNull(User::find($user->id)->customer_id);
    }

    public function testDestroy3(): void
    {
        $customer = factory(Customer::class)->create();
        $transaction = factory(Transaction::class)->create(['customer_id' => $customer->id]);

        self::assertEquals(1, Transaction::whereCustomerId($customer->id)->count());

        $response = $this->deleteJson('/api/customers/' . $customer->id);
        $response->assertStatus(204);
        // Check the 'ON DELETE SET NULL' behaviour of transactions' customer_id
        self::assertEquals(0, Transaction::whereCustomerId($customer->id)->count());
        self::assertNull(Transaction::find($transaction->id)->customer_id);
    }
}
