<?php

namespace Tests\Feature;

use App\PaymentMethod;
use App\Transaction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PaymentMethodsRoutingTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @return void
     */
    public function testIndex(): void
    {
        PaymentMethod::factory()->count(2)->create();

        $response = $this->get('/api/payment-methods');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    0 => [
                        'id',
                        'name',
                        'debitCustomer',
                        'iconName',
                        'parameters',
                    ],
                    1 => [
                        'id',
                        'name',
                        'debitCustomer',
                        'iconName',
                        'parameters',
                    ]
                ]
            ]);
    }

    public function testCreate(): void
    {
        $response = $this->postJson('/api/payment-methods', [
            'name' => 'PaymentMethod',
            'debit_customer' => true,
            'icon_name' => 'payment',
            'parameters' => ['icon_type' => 'fontawesome'],
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'name',
                    'debitCustomer',
                    'iconName',
                    'parameters',
                ]
            ]);
    }

    public function testUpdate1(): void
    {
        $paymentMethod = PaymentMethod::factory()->create();

        $response = $this->putJson('/api/payment-methods/' . $paymentMethod->id, [
            'name' => 'PaymentMethod',
            'debit_customer' => true,
            'icon_name' => 'payment',
            'parameters' => ['icon_type' => 'fontawesome'],
        ]);

        $response->assertStatus(200)
            ->assertJson(['data' => [
                'id' => $paymentMethod->id,
                'name' => 'PaymentMethod',
                'debitCustomer' => true,
                'iconName' => 'payment',
                'parameters' => '{"icon_type":"fontawesome"}',
            ]]);
    }

    public function testUpdate2(): void
    {
        $response = $this->putJson('/api/payment-methods/0');
        $response->assertStatus(404);
    }

    public function testShow1(): void
    {
        $paymentMethod = PaymentMethod::factory()->create();

        $response = $this->getJson('/api/payment-methods/' . $paymentMethod->id);

        $response->assertStatus(200)
            ->assertJsonStructure(['data' => [
                'id',
                'name',
                'debitCustomer',
                'iconName',
                'parameters',
            ]])
            ->assertJson(['data' => [
                'id' => $paymentMethod->id,
                'name' => $paymentMethod->name,
                'debitCustomer' => $paymentMethod->debit_customer,
                'iconName' => $paymentMethod->icon_name,
                'parameters' => $paymentMethod->parameters,
            ]]);
    }

    public function testShow2(): void
    {
        $response = $this->getJson('/api/payment-methods/0');
        $response->assertStatus(404);
    }

    public function testDestroy1(): void
    {
        $paymentMethod = PaymentMethod::factory()->create();

        $response = $this->deleteJson('/api/payment-methods/' . $paymentMethod->id);
        $response->assertStatus(204);
        // Check whether the resource is unreachable
        $response = $this->deleteJson('/api/payment-methods/' . $paymentMethod->id);
        $response->assertStatus(404);
        $response = $this->putJson('/api/payment-methods/' . $paymentMethod->id);
        $response->assertStatus(404);
        $response = $this->getJson('/api/payment-methods/' . $paymentMethod->id);
        $response->assertStatus(404);
        // Check the destruction in database
        self::assertNull(PaymentMethod::find($paymentMethod->id));
    }

    public function testDestroy2(): void
    {
        $paymentMethod = PaymentMethod::factory()->create();
        $transaction = Transaction::factory()->create(['payment_method_id' => $paymentMethod->id]);

        self::assertEquals(1, Transaction::wherePaymentMethodId($paymentMethod->id)->count());

        $response = $this->deleteJson('/api/payment-methods/' . $paymentMethod->id);
        $response->assertStatus(204);
        // Check the 'ON DELETE SET NULL' behaviour of article's supplier_id
        self::assertEquals(0, Transaction::wherePaymentMethodId($paymentMethod->id)->count());
        self::assertNull(Transaction::find($transaction->id)->payment_method_id);
    }
}
