<?php

namespace Tests\Feature;

use App\PaymentMethod;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PaymentMethodsRoutingTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @return void
     */
    public function testIndex()
    {
        factory(PaymentMethod::class, 2)->create();

        $response = $this->get('/api/payment-methods');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    0 => [
                        'id',
                        'name',
                        'needsCashDrawer',
                        'iconName',
                        'parameters',
                    ],
                    1 => [
                        'id',
                        'name',
                        'needsCashDrawer',
                        'iconName',
                        'parameters',
                    ]
                ]
            ]);
    }

    public function testCreate()
    {
        $response = $this->postJson('/api/payment-methods', [
            'name' => 'PaymentMethod McTest',
            'needs_cash_drawer' => true,
            'icon_name' => 'payment',
            'parameters' => ['icon_type' => 'fontawesome'],
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'name',
                    'needsCashDrawer',
                    'iconName',
                    'parameters',
                ]
            ]);
    }

    public function testUpdate()
    {
        $paymentMethod = factory(PaymentMethod::class)->create();

        $response = $this->putJson('/api/payment-methods/' . $paymentMethod->id, [
            'name' => 'PaymentMethod McTest',
            'needs_cash_drawer' => true,
            'icon_name' => 'payment',
            'parameters' => ['icon_type' => 'fontawesome'],
        ]);

        $response->assertStatus(200)
            ->assertJson(['data' => [
                'id' => $paymentMethod->id,
                'name' => 'PaymentMethod McTest',
                'needsCashDrawer' => '1',
                'iconName' => 'payment',
                'parameters' => '{"icon_type":"fontawesome"}',
            ]]);
    }

    public function testShow()
    {
        $paymentMethod = factory(PaymentMethod::class)->create();

        $response = $this->getJson('/api/payment-methods/' . $paymentMethod->id);

        $response->assertStatus(200)
            ->assertJsonStructure(['data' => [
                'id',
                'name',
                'needsCashDrawer',
                'iconName',
                'parameters',
            ]]);
    }

    public function testDestroy()
    {
        $paymentMethod = factory(PaymentMethod::class)->create();

        $response = $this->deleteJson('/api/payment-methods/' . $paymentMethod->id);
        $response->assertStatus(204);

        $response = $this->getJson('/api/payment-methods/' . $paymentMethod->id);
        $response->assertStatus(404);
    }
}
