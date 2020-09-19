<?php

namespace Tests\Feature;

use App\Models\Food;
use App\Models\Price;
use App\Models\Supplier;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FoodRoutingTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @return void
     */
    public function testIndex(): void
    {
        Food::factory()
            ->count(2)
            ->hasPrices(1)
            ->create();

        $response = $this->get('/api/food');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    0 => [
                        'id',
                        'name',
                        'quantity',
                        'unitPrice',
                        'isBulk',
                        'price' => [
                            'id', 'value',
                        ],
                    ],
                    1 => [
                        'id',
                        'name',
                        'quantity',
                        'unitPrice',
                        'isBulk',
                        'price' => [
                            'id', 'value',
                        ],
                    ]
                ]
            ]);
    }

    public function testCreate(): void
    {
        $supplier = Supplier::factory()->create();

        $response = $this->postJson('/api/food', [
            'name' => 'Food',
            'quantity' => 42,
            'supplier_id' => $supplier->id,
            'unit_price' => 142.42,
            'value' => 4.2,
            'is_bulk' => 1,
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'name',
                    'quantity',
                    'unitPrice',
                    'isBulk',
                    'price' => [
                        'id', 'value',
                    ],
                    'supplier' => [
                        'id', 'name', 'notes',
                        'address', 'phone', 'email', 'supplierSince',
                    ],
                ]
            ])
            ->assertJson([
                'data' => [
                    'name' => 'Food',
                    'quantity' => 42,
                    'unitPrice' => 142.42,
                    'isBulk' => 1,
                    'price' => [
                        'value' => 4.2,
                    ],
                    'supplier' => [
                        'id' => $supplier->id,
                        'name' => $supplier->name,
                        'notes' => $supplier->notes,
                        'address' => $supplier->address,
                        'phone' => $supplier->phone,
                        'email' => $supplier->email,
                        'supplierSince' => $supplier->supplier_since->toISOString(),
                    ],
                ]
            ]);
    }

    public function testUpdate1(): void
    {
        $food = Food::factory()
            ->state(['is_bulk' => false])
            ->create();
        $price = Price::factory()->make();
        $food->prices()->save($price);

        $newSupplier = Supplier::factory()->create();
        $id = $food->id;

        $response = $this->putJson('/api/food/' . $id, [
            'name' => 'Food',
            'quantity' => 42,
            'supplier_id' => $newSupplier->id,
            'unit_price' => 142.42,
            'value' => 4.2,
            'is_bulk' => 1,
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'id' => $id,
                    'name' => 'Food',
                    'quantity' => 42,
                    'unitPrice' => 142.42,
                    'isBulk' => true,
                    'price' => [
                        'id' => $price->id + 1,
                        'value' => 4.2,
                    ],
                    'priceHistory' => [
                        [
                            'id' => $price->id,
                            'value' => $price->value,
                            'createdAt' => $price->created_at->toISOString(),
                        ],
                        [
                            'id' => $price->id + 1,
                            'value' => 4.2,
                            'createdAt' => $price->created_at->toISOString(),
                        ],
                    ],
                    'supplier' => [
                        'id' => $newSupplier->id,
                        'name' => $newSupplier->name,
                        'notes' => $newSupplier->notes,
                        'address' => $newSupplier->address,
                        'phone' => $newSupplier->phone,
                        'email' => $newSupplier->email,
                        'supplierSince' => $newSupplier->supplier_since->toISOString(),
                    ],
                ]
            ]);
    }

    public function testUpdate2(): void
    {
        $response = $this->putJson('/api/food/0');
        $response->assertStatus(404);
    }

    public function testUpdate3(): void
    {
        $food = Food::factory()->create();
        $price = Price::factory()->make();
        $food->prices()->save($price);

        $response = $this->putJson('/api/food/' . $food->id, [
            'name' => 'Food',
        ]);
        $response->assertStatus(200);
    }

    public function testShow1(): void
    {
        $supplier = Supplier::factory()->create();
        $food = Food::factory()
            ->state(['supplier_id' => $supplier->id, 'is_bulk' => false])
            ->create();
        $price = Price::factory()->make();
        $food->prices()->save($price);

        $id = $food->id;
        $response = $this->getJson('/api/food/' . $id);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'name',
                    'quantity',
                    'unitPrice',
                    'isBulk',
                    'price' => [
                        'id', 'value',
                    ],
                    'priceHistory',
                    'supplier' => [
                        'id', 'name', 'notes',
                        'address', 'phone', 'email', 'supplierSince',
                    ],
                ]
            ])
            ->assertJson([
                'data' => [
                    'id' => $id,
                    'name' => $food->name,
                    'quantity' => $food->quantity,
                    'unitPrice' => $food->unit_price,
                    'isBulk' => false,
                    'price' => [
                        'id' => $price->id,
                        'value' => $price->value,
                    ],
                    'priceHistory' => [
                        [
                            'id' => $price->id,
                            'value' => $price->value,
                            'createdAt' => $price->created_at->toISOString(),
                        ],
                    ],
                    'supplier' => [
                        'id' => $supplier->id,
                        'name' => $supplier->name,
                        'notes' => $supplier->notes,
                        'address' => $supplier->address,
                        'phone' => $supplier->phone,
                        'email' => $supplier->email,
                        'supplierSince' => $supplier->supplier_since->toISOString(),
                    ],
                ]
            ]);
    }

    public function testShow2(): void
    {
        $response = $this->getJson('/api/food/0');
        $response->assertStatus(404);
    }

    public function testDestroy(): void
    {
        $food = Food::factory()
            ->hasPrices(1)
            ->create();

        $id = $food->id;
        $response = $this->deleteJson('/api/food/' . $id);
        $response->assertStatus(204);
        // Check whether the resource is unreachable
        $response = $this->deleteJson('/api/food/' . $id);
        $response->assertStatus(404);
        $response = $this->putJson('/api/food/' . $id);
        $response->assertStatus(404);
        $response = $this->getJson('/api/food/' . $id);
        $response->assertStatus(404);
        // Check the soft delete success
        self::assertNotNull(Food::onlyTrashed()->find($id));
    }
}
