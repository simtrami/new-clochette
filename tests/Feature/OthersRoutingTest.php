<?php

namespace Tests\Feature;

use App\Other;
use App\Price;
use App\Supplier;
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
        $supplier = factory(Supplier::class)->create();

        factory(Other::class, 2)->create(['supplier_id' => $supplier->id])
            ->each(function ($other) {
                $other->prices()->save(factory(Price::class)->make());
            });

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
        $supplier = factory(Supplier::class)->create();

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
                        'id', 'name', 'description',
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
                        'description' => $supplier->description,
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
        $supplier_1 = factory(Supplier::class)->create();
        $supplier_2 = factory(Supplier::class)->create();

        $other = factory(Other::class)->create(['supplier_id' => $supplier_1->id]);
        $id = $other->id;
        $price = factory(Price::class)->make();
        $other->prices()->save($price);

        $response = $this->putJson('/api/others/' . $id, [
            'name' => 'Other',
            'quantity' => 42,
            'supplier_id' => $supplier_2->id,
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
                        'id' => $supplier_2->id,
                        'name' => $supplier_2->name,
                        'description' => $supplier_2->description,
                        'address' => $supplier_2->address,
                        'phone' => $supplier_2->phone,
                        'email' => $supplier_2->email,
                        'supplierSince' => $supplier_2->supplier_since->toISOString(),
                    ],
                ]
            ]);
    }

    public function testUpdate2(): void
    {
        $response = $this->putJson('/api/others/0');
        $response->assertStatus(404);
    }

    public function testShow1(): void
    {
        $supplier = factory(Supplier::class)->create();

        $other = factory(Other::class)->create(['supplier_id' => $supplier->id]);
        $id = $other->id;
        $price = factory(Price::class)->make();
        $other->prices()->save($price);

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
                        'id', 'name', 'description',
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
                        'description' => $supplier->description,
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
        $supplier = factory(Supplier::class)->create();
        $other = factory(Other::class)->create(['supplier_id' => $supplier->id]);
        $id = $other->id;
        $other->prices()->save(factory(Price::class)->make());

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
