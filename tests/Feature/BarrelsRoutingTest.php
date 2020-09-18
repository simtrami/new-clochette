<?php

namespace Tests\Feature;

use App\Barrel;
use App\Price;
use App\Supplier;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BarrelsRoutingTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @return void
     */
    public function testIndex(): void
    {
        $supplier = factory(Supplier::class)->create();

        $barrel_1 = factory(Barrel::class)->create(['supplier_id' => $supplier->id]);
        $barrel_1->prices()->save(factory(Price::class)->make(['second_value' => 4.2]));

        $barrel_2 = factory(Barrel::class)->create(['supplier_id' => $supplier->id]);
        $barrel_2->prices()->save(factory(Price::class)->make());

        $response = $this->get('/api/barrels');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    0 => [
                        'id',
                        'name',
                        'quantity',
                        'unitPrice',
                        'price' => [
                            'id', 'value', 'secondValue',
                        ],
                        'volume',
                        'coupler',
                        'abv', 'ibu',
                    ],
                    1 => [
                        'id',
                        'name',
                        'quantity',
                        'unitPrice',
                        'price' => [
                            'id', 'value', 'secondValue',
                        ],
                        'volume',
                        'coupler',
                        'abv', 'ibu',
                    ]
                ]
            ]);
    }

    public function testCreate(): void
    {
        $supplier = factory(Supplier::class)->create();

        $response = $this->postJson('/api/barrels', [
            'name' => 'Barrel',
            'quantity' => 42,
            'value' => 4.2,
            'second_value' => 2.6,
            'volume' => 30,
            'coupler' => 'KeyKeg',
            'abv' => 4.5,
            'ibu' => 42.5,
            'unit_price' => 142.42,
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
                        'id', 'value', 'secondValue',
                    ],
                    'volume',
                    'coupler',
                    'abv', 'ibu',
                    'supplier' => [
                        'id', 'name', 'description',
                        'address', 'phone', 'email', 'supplierSince',
                    ],
                ]
            ])
            ->assertJson([
                'data' => [
                    'name' => 'Barrel',
                    'quantity' => 42,
                    'unitPrice' => 142.42,
                    'price' => [
                        'value' => 4.20,
                        'secondValue' => 2.6,
                    ],
                    'volume' => 30,
                    'coupler' => 'KeyKeg',
                    'abv' => 4.5,
                    'ibu' => 42.5,
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

        $barrel = factory(Barrel::class)->create(['supplier_id' => $supplier_1->id]);
        $id = $barrel->id;
        $price = factory(Price::class)->make(['second_value' => 3.4]);
        $barrel->prices()->save($price);

        $response = $this->putJson('/api/barrels/' . $id, [
            'name' => 'Barrel',
            'quantity' => 42,
            'supplier_id' => $supplier_2->id,
            'unit_price' => 142.42,
            'value' => 4.2,
            'second_value' => 2.6,
            'volume' => 30,
            'coupler' => 'KeyKeg',
            'abv' => 4.55,
            'ibu' => 42.5,
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'name',
                    'quantity',
                    'unitPrice',
                    'price' => [
                        'id', 'value', 'secondValue',
                    ],
                    'priceHistory' => [
                        ['id', 'value', 'secondValue', 'createdAt'],
                        ['id', 'value', 'secondValue', 'createdAt'],
                    ],
                    'volume',
                    'coupler',
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
                    'name' => 'Barrel',
                    'quantity' => 42,
                    'unitPrice' => 142.42,
                    'price' => [
                        'id' => $price->id + 1,
                        'value' => 4.20,
                        'secondValue' => 2.60,
                    ],
                    'priceHistory' => [
                        [
                            'id' => $price->id,
                            'value' => $price->value,
                            'secondValue' => $price->second_value,
                            'createdAt' => $price->created_at->toISOString(),
                        ],
                        [
                            'id' => $price->id + 1,
                            'value' => 4.20,
                            'secondValue' => 2.60,
                            'createdAt' => $price->created_at->toISOString(),
                        ],
                    ],
                    'volume' => 30,
                    'coupler' => 'KeyKeg',
                    'abv' => 4.55,
                    'ibu' => 42.5,
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
        $response = $this->putJson('/api/barrels/0');
        $response->assertStatus(404);
    }

    public function testShow1(): void
    {
        $supplier = factory(Supplier::class)->create();
        $barrel = factory(Barrel::class)->create(['supplier_id' => $supplier->id]);
        $id = $barrel->id;
        $price = factory(Price::class)->make(['second_value' => 3.4]);
        $barrel->prices()->save($price);

        $response = $this->getJson('/api/barrels/' . $id);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'name',
                    'quantity',
                    'unitPrice',
                    'price' => [
                        'id', 'value', 'secondValue',
                    ],
                    'priceHistory' => [
                        ['id', 'value', 'secondValue', 'createdAt'],
                    ],
                    'volume',
                    'coupler',
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
                    'name' => $barrel->name,
                    'quantity' => $barrel->quantity,
                    'unitPrice' => $barrel->unit_price,
                    'price' => [
                        'id' => $price->id,
                        'value' => $price->value,
                        'secondValue' => $price->second_value,
                    ],
                    'priceHistory' => [
                        [
                            'id' => $price->id,
                            'value' => $price->value,
                            'secondValue' => $price->second_value,
                            'createdAt' => $price->created_at->toISOString(),
                        ],
                    ],
                    'volume' => $barrel->volume,
                    'coupler' => $barrel->coupler,
                    'abv' => $barrel->abv,
                    'ibu' => $barrel->ibu,
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
        $response = $this->getJson('/api/barrels/0');
        $response->assertStatus(404);
    }

    public function testDestroy(): void
    {
        $supplier = factory(Supplier::class)->create();
        $barrel = factory(Barrel::class)->create(['supplier_id' => $supplier->id]);
        $id = $barrel->id;
        $barrel->prices()->save(factory(Price::class)->make(['second_value' => 3.4]));

        $response = $this->deleteJson('/api/barrels/' . $id);
        $response->assertStatus(204);
        // Check whether the resource is unreachable
        $response = $this->deleteJson('/api/barrels/' . $id);
        $response->assertStatus(404);
        $response = $this->putJson('/api/barrels/' . $id);
        $response->assertStatus(404);
        $response = $this->getJson('/api/barrels/' . $id);
        $response->assertStatus(404);
        // Check the soft delete success
        self::assertNotNull(Barrel::onlyTrashed()->find($id));
    }
}
