<?php

namespace Tests\Feature;

use App\Customer;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UsersRoutingTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @return void
     */
    public function testIndex(): void
    {
        User::factory()->count(2)->create();

        $response = $this->get('/api/users');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    0 => [
                        'id',
                        'name',
                        'username',
                        'email',
                        'roles',
                        'permissions',
                    ],
                    1 => [
                        'id',
                        'name',
                        'username',
                        'email',
                        'roles',
                        'permissions',
                    ]
                ]
            ]);

        User::factory()
            ->forCustomer()
            ->create();

        $response = $this->get('/api/users');
        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    0 => [
                        'id',
                        'name',
                        'username',
                        'email',
                        'roles',
                        'permissions',
                    ],
                    1 => [
                        'id',
                        'name',
                        'username',
                        'email',
                        'roles',
                        'permissions',
                    ],
                    2 => [
                        'id',
                        'name',
                        'username',
                        // customer is not supposed to be present as it is not loaded by default
                        'email',
                        'roles',
                        'permissions',
                    ]
                ]
            ]);

    }

    public function testCreate1(): void
    {
        $response = $this->postJson('/api/users', [
            'name' => 'User McTest',
            'username' => 'user.mctest',
            'email' => 'user@mctest.com',
            'password' => 'secret',
            'password_confirmation' => 'secret',
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'name',
                    'username',
                    'customer',
                    'email',
                    'roles',
                    'permissions',
                ]
            ])
            ->assertJson([
                'data' => [
                    'name' => 'User McTest',
                    'username' => 'user.mctest',
                    'customer' => null,
                    'email' => 'user@mctest.com',
                    'roles' => [],
                    'permissions' => [],
                ]
            ]);
    }

    public function testCreate2(): void
    {
        $customer = Customer::factory()->create();

        $response = $this->postJson('/api/users', [
            'name' => 'User McTest',
            'username' => 'user.mctest',
            'email' => 'user@mctest.com',
            'password' => 'secret',
            'password_confirmation' => 'secret',
            'customer_id' => $customer->id,
        ]);

        $response->assertStatus(201)
            ->assertJson([
                'data' => [
                    'name' => 'User McTest',
                    'username' => 'user.mctest',
                    'email' => 'user@mctest.com',
                    'roles' => [],
                    'permissions' => [],
                ]
            ])
            ->assertJsonMissingExact([
                'customer' => null,
            ]);
    }

    public function testUpdate1(): void
    {
        $user = User::factory()->create();

        $response = $this->putJson('/api/users/' . $user->id, [
            'name' => 'User McTest',
            'username' => 'user.mctest',
            'email' => 'user@mctest.com',
            'password' => 'secret',
            'password_confirmation' => 'secret'
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'name',
                    'username',
                    'customer',
                    'email',
                    'roles',
                    'permissions',
                ]
            ])
            ->assertJson(['data' => [
                'id' => $user->id,
                'name' => 'User McTest',
                'username' => 'user.mctest',
                'email' => 'user@mctest.com',
                'customer' => null,
            ]]);
    }

    public function testUpdate2(): void
    {
        $response = $this->putJson('/api/users/0');
        $response->assertStatus(404);
    }

    public function testUpdate3(): void
    {
        $user = User::factory()->create();
        $customer = Customer::factory()->create();

        $response = $this->putJson('/api/users/' . $user->id, [
            'name' => 'User McTest',
            'username' => 'user.mctest',
            'customer_id' => $customer->id,
            'email' => 'user@mctest.com',
            'password' => 'secret',
            'password_confirmation' => 'secret'
        ]);

        $response->assertStatus(200)
            ->assertJson(['data' => [
                'id' => $user->id,
                'name' => 'User McTest',
                'username' => 'user.mctest',
                'email' => 'user@mctest.com',
            ]])
            ->assertJsonMissingExact([
                'customer' => null,
            ]);
    }

    public function testUpdate4(): void
    {
        $user = User::factory()
            ->forCustomer()
            ->create();

        $response = $this->putJson('/api/users/' . $user->id, [
            'name' => 'User McTest',
            'username' => 'user.mctest',
            'customer_id' => null,
            'email' => 'user@mctest.com',
            'password' => 'secret',
            'password_confirmation' => 'secret'
        ]);

        $response->assertStatus(200)
            ->assertJson(['data' => [
                'id' => $user->id,
                'name' => 'User McTest',
                'username' => 'user.mctest',
                'email' => 'user@mctest.com',
                'customer' => null,
            ]]);
    }

    public function testShow1(): void
    {
        $user = User::factory()->create();

        $response = $this->getJson('/api/users/' . $user->id);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'name',
                    'username',
                    'customer',
                    'email',
                    'roles',
                    'permissions',
                ]
            ])
            ->assertJsonFragment([
                'customer' => null,
            ]);
    }

    public function testShow2(): void
    {
        $response = $this->getJson('/api/users/0');
        $response->assertStatus(404);
    }

    public function testShow3(): void
    {
        $user = User::factory()
            ->forCustomer()
            ->create();

        $response = $this->getJson('/api/users/' . $user->id);

        $response->assertStatus(200)
            ->assertJsonMissingExact([
                'customer' => null,
            ]);
    }

    public function testDestroy(): void
    {
        $user = User::factory()->create();

        $response = $this->deleteJson('/api/users/' . $user->id);
        $response->assertStatus(204);
        // Check whether the resource is unreachable
        $response = $this->deleteJson('/api/users/' . $user->id);
        $response->assertStatus(404);
        $response = $this->putJson('/api/users/' . $user->id);
        $response->assertStatus(404);
        $response = $this->getJson('/api/users/' . $user->id);
        $response->assertStatus(404);
        // Check the destruction in database
        self::assertNull(User::find($user->id));
    }
}
