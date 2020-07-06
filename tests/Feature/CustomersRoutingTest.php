<?php

namespace Tests\Feature;

use App\Customer;
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
                    'isStaff'
                ]
            ]);
    }

    public function testUpdate(): void
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

    public function testShow(): void
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

    public function testDestroy(): void
    {
        $customer = factory(Customer::class)->create();

        $response = $this->deleteJson('/api/customers/' . $customer->id);

        $response->assertStatus(204);
    }
}
