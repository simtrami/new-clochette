<?php

namespace Tests\Feature;

use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UsersRoutingTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @return void
     */
    public function testIndex()
    {
        factory(User::class, 2)->create();

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
                        'permissions'
                    ],
                    1 => [
                        'id',
                        'name',
                        'username',
                        'email',
                        'roles',
                        'permissions'
                    ]
                ]
            ]);
    }

    public function testCreate()
    {
        $response = $this->postJson('/api/users', [
            'name' => 'User McTest',
            'username' => 'user.mctest',
            'email' => 'user@mctest.com',
            'password' => 'secret',
            'password_confirmation' => 'secret'
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'name',
                    'username',
                    'email',
                    'roles',
                    'permissions'
                ]
            ]);
    }

    public function testUpdate()
    {
        $user = factory(User::class)->create();

        $response = $this->putJson('/api/users/' . $user->id, [
            'name' => 'User McTest',
            'username' => 'user.mctest',
            'email' => 'user@mctest.com',
            'password' => 'secret',
            'password_confirmation' => 'secret'
        ]);

        $response->assertStatus(200)
            ->assertJson(['data' => [
                'id' => $user->id,
                'name' => 'User McTest',
                'username' => 'user.mctest',
                'email' => 'user@mctest.com'
            ]]);
    }

    public function testShow()
    {
        $user = factory(User::class)->create();

        $response = $this->getJson('/api/users/' . $user->id);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'name',
                    'username',
                    'email',
                    'roles',
                    'permissions'
                ]
            ]);
    }

    public function testDestroy()
    {
        $user = factory(User::class)->create();

        $response = $this->deleteJson('/api/users/' . $user->id);

        $response->assertStatus(204);
    }
}
