<?php

namespace Tests\Feature;

use App\Models\Other;
use App\Models\Price;
use App\Models\Supplier;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OthersRoutingTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @return void
     */
    public function testIndex(): void
    {
        Other::factory()
            ->count(2)
            ->hasPrices(1)
            ->create();

        $response = $this->get('/api/others');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    0 => [
                        'id',
                        'name',
                        'quantity',
                        'unitPrice',
                        'description',
                        'price' => [
                            'id', 'value',
                        ],
                    ],
                    1 => [
                        'id',
                        'name',
                        'quantity',
                        'unitPrice',
                        'description',
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

        $response = $this->postJson('/api/others', [
            'name' => 'Other',
            'quantity' => 42,
            'supplier_id' => $supplier->id,
            'unit_price' => 142.42,
            'value' => 4.2,
            'description' => 'I am another thing you can buy.',
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'name',
                    'quantity',
                    'unitPrice',
                    'description',
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
                    'name' => 'Other',
                    'quantity' => 42,
                    'unitPrice' => 142.42,
                    'description' => 'I am another thing you can buy.',
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
        $other = Other::factory()
            ->create();
        $price = Price::factory()->make();
        $other->prices()->save($price);

        $newSupplier = Supplier::factory()->create();
        $id = $other->id;

        $response = $this->putJson('/api/others/' . $id, [
            'name' => 'Other',
            'quantity' => 42,
            'supplier_id' => $newSupplier->id,
            'unit_price' => 142.42,
            'value' => 4.2,
            'description' => 'I am another thing you can buy.',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'id' => $id,
                    'name' => 'Other',
                    'quantity' => 42,
                    'unitPrice' => 142.42,
                    'description' => 'I am another thing you can buy.',
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
        $response = $this->putJson('/api/others/0');
        $response->assertStatus(404);
    }

    public function testUpdate3(): void
    {
        $other = Other::factory()->create();
        $price = Price::factory()->make();
        $other->prices()->save($price);

        $response = $this->putJson('/api/others/' . $other->id, [
            'name' => 'Other',
        ]);
        $response->assertStatus(200);
    }

    public function testShow1(): void
    {
        $supplier = Supplier::factory()->create();
        $other = Other::factory()
            ->state(['supplier_id' => $supplier->id])
            ->create();
        $price = Price::factory()->make();
        $other->prices()->save($price);

        $id = $other->id;
        $response = $this->getJson('/api/others/' . $id);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'name',
                    'quantity',
                    'unitPrice',
                    'description',
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
                    'name' => $other->name,
                    'quantity' => $other->quantity,
                    'unitPrice' => $other->unit_price,
                    'description' => $other->description,
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
        $response = $this->getJson('/api/others/0');
        $response->assertStatus(404);
    }

    public function testDestroy(): void
    {
        $other = Other::factory()
            ->hasPrices(1)
            ->create();

        $id = $other->id;
        $response = $this->deleteJson('/api/others/' . $id);
        $response->assertStatus(204);
        // Check whether the resource is unreachable
        $response = $this->deleteJson('/api/others/' . $id);
        $response->assertStatus(404);
        $response = $this->putJson('/api/others/' . $id);
        $response->assertStatus(404);
        $response = $this->getJson('/api/others/' . $id);
        $response->assertStatus(404);
        // Check the soft delete success
        self::assertNotNull(Other::onlyTrashed()->find($id));
    }
}
