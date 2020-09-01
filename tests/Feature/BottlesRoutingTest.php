<?php

namespace Tests\Feature;

use App\Bottle;
use App\Price;
use App\Supplier;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BottlesRoutingTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @return void
     */
    public function testIndex(): void
    {
        $supplier = factory(Supplier::class)->create();

        factory(Bottle::class, 2)
            ->create(['supplier_id' => $supplier->id])
            ->each(function ($bottle) {
                $bottle->prices()->save(factory(Price::class)->make());
            });

        $response = $this->get('/api/bottles');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    0 => [
                        'id',
                        'name',
                        'quantity',
                        'unitPrice',
                        'price' => [
                            'id', 'value',
                        ],
                        'volume',
                        'isReturnable',
                        'abv', 'ibu',
                    ],
                    1 => [
                        'id',
                        'name',
                        'quantity',
                        'unitPrice',
                        'price' => [
                            'id', 'value',
                        ],
                        'volume',
                        'isReturnable',
                        'abv', 'ibu',
                    ]
                ]
            ]);
    }

    public function testCreate(): void
    {
        $supplier = factory(Supplier::class)->create();

        $response = $this->postJson('/api/bottles', [
            'name' => 'Bottle',
            'quantity' => 42,
            'unit_price' => 142.42,
            'value' => 4.2,
            'volume' => 30,
            'is_returnable' => 1,
            'abv' => 3.4,
            'ibu' => 32.4,
            'supplier_id' => $supplier->id,
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'name',
                    'quantity',
                    'unitPrice',
                    'price' => [
                        'id', 'value',
                    ],
                    'volume',
                    'isReturnable',
                    'abv', 'ibu',
                    'supplier' => [
                        'id', 'name', 'description',
                        'address', 'phone', 'email', 'supplierSince',
                    ],
                ]
            ])
            ->assertJson([
                'data' => [
                    'name' => 'Bottle',
                    'quantity' => 42,
                    'unitPrice' => 142.42,
                    'price' => [
                        'value' => 4.20,
                    ],
                    'volume' => 30,
                    'isReturnable' => 1,
                    'abv' => 3.4,
                    'ibu' => 32.4,
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

        $bottle = factory(Bottle::class)->create(['supplier_id' => $supplier_1->id, 'is_returnable' => false]);
        $id = $bottle->id;
        $price = factory(Price::class)->make();
        $bottle->prices()->save($price);


        $response = $this->putJson('/api/bottles/' . $id, [
            'name' => 'Bottle',
            'quantity' => 42,
            'supplier_id' => $supplier_2->id,
            'unit_price' => 142.42,
            'value' => 4.2,
            'volume' => 30,
            'is_returnable' => 1,
            'abv' => 3.4,
            'ibu' => 32.4,
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'id' => $id,
                    'name' => 'Bottle',
                    'quantity' => 42,
                    'unitPrice' => 142.42,
                    'price' => [
                        'id' => $price->id + 1,
                        'value' => 4.20,
                    ],
                    'priceHistory' => [
                        [
                            'id' => $price->id,
                            'value' => $price->value,
                            'isActive' => false,
                            'createdAt' => $price->created_at->toISOString(),
                        ],
                        [
                            'id' => $price->id + 1,
                            'value' => 4.2,
                            'isActive' => true,
                            'createdAt' => $price->created_at->toISOString(),
                        ],
                    ],
                    'volume' => 30,
                    'isReturnable' => true,
                    'abv' => 3.4,
                    'ibu' => 32.4,
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
        $response = $this->putJson('/api/bottles/0');
        $response->assertStatus(404);
    }

    public function testShow1(): void
    {
        $supplier = factory(Supplier::class)->create();

        $bottle = factory(Bottle::class)->create(['supplier_id' => $supplier->id, 'is_returnable' => false]);
        $id = $bottle->id;
        $price = factory(Price::class)->make();
        $bottle->prices()->save($price);

        $response = $this->getJson('/api/bottles/' . $id);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'name',
                    'quantity',
                    'unitPrice',
                    'price' => [
                        'id', 'value',
                    ],
                    'priceHistory',
                    'volume',
                    'isReturnable',
                    'abv', 'ibu',
                    'supplier' => [
                        'id', 'name', 'description',
                        'address', 'phone', 'email', 'supplierSince',
                    ],
                ]
            ])
            ->assertJson([
                'data' => [
                    'id' => $id,
                    'name' => $bottle->name,
                    'quantity' => $bottle->quantity,
                    'unitPrice' => $bottle->unit_price,
                    'price' => [
                        'id' => $price->id,
                        'value' => $price->value,
                    ],
                    'priceHistory' => [
                        [
                            'id' => $price->id,
                            'value' => $price->value,
                            'isActive' => true,
                            'createdAt' => $price->created_at->toISOString(),
                        ],
                    ],
                    'volume' => $bottle->volume,
                    'isReturnable' => false,
                    'abv' => $bottle->abv,
                    'ibu' => $bottle->ibu,
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
        $response = $this->getJson('/api/bottles/0');
        $response->assertStatus(404);
    }

    public function testDestroy(): void
    {
        $supplier = factory(Supplier::class)->create();
        $bottle = factory(Bottle::class)->create(['supplier_id' => $supplier->id]);
        $id = $bottle->id;
        $bottle->prices()->save(factory(Price::class)->make());

        $response = $this->deleteJson('/api/bottles/' . $id);
        $response->assertStatus(204);
        // Check whether the resource is unreachable
        $response = $this->deleteJson('/api/bottles/' . $id);
        $response->assertStatus(404);
        $response = $this->putJson('/api/bottles/' . $id);
        $response->assertStatus(404);
        $response = $this->getJson('/api/bottles/' . $id);
        $response->assertStatus(404);
        // Check the soft delete success
        self::assertNotNull(Bottle::onlyTrashed()->find($id));
    }
}
