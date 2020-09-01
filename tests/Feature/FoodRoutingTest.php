<?php

namespace Tests\Feature;

use App\Food;
use App\Price;
use App\Supplier;
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
        $supplier = factory(Supplier::class)->create();

        factory(Food::class, 2)->create(['supplier_id' => $supplier->id])
            ->each(function ($food) {
                $food->prices()->save(factory(Price::class)->make());
            });

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
        $supplier = factory(Supplier::class)->create();

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
                        'id', 'name', 'description',
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

        $food = factory(Food::class)->create(['supplier_id' => $supplier_1->id, 'is_bulk' => false]);
        $id = $food->id;
        $price = factory(Price::class)->make();
        $food->prices()->save($price);


        $response = $this->putJson('/api/food/' . $id, [
            'name' => 'Food',
            'quantity' => 42,
            'supplier_id' => $supplier_2->id,
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
        $response = $this->putJson('/api/food/0');
        $response->assertStatus(404);
    }

    public function testShow1(): void
    {
        $supplier = factory(Supplier::class)->create();

        $food = factory(Food::class)->create(['supplier_id' => $supplier->id, 'is_bulk' => false]);
        $id = $food->id;
        $price = factory(Price::class)->make();
        $food->prices()->save($price);

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
                        'id', 'name', 'description',
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
                            'isActive' => true,
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
        $response = $this->getJson('/api/food/0');
        $response->assertStatus(404);
    }

    public function testDestroy(): void
    {
        $supplier = factory(Supplier::class)->create();
        $food = factory(Food::class)->create(['supplier_id' => $supplier->id]);
        $id = $food->id;
        $food->prices()->save(factory(Price::class)->make());

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
