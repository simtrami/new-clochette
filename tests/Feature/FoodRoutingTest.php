<?php

namespace Tests\Feature;

use App\Article;
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

        $articles = factory(Article::class, 2)
            ->create(['supplier_id' => $supplier->id])
            ->each(function ($article) {
                $article->prices()->save(factory(Price::class)->make());
                $article->food()->save(factory(Food::class)->make());
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
                        'price' => [
                            'id', 'value',
                        ],
                        'isBulk',
                    ],
                    1 => [
                        'id',
                        'name',
                        'quantity',
                        'unitPrice',
                        'price' => [
                            'id', 'value',
                        ],
                        'isBulk',
                    ]
                ]
            ]);
    }

    public function testCreate(): void
    {
        $supplier = factory(Supplier::class)->create();

        $response = $this->postJson('/api/food', [
            'name' => 'Food',
            'quantity' => '42',
            'supplier_id' => $supplier->id,
            'unit_price' => '142.42',
            'value' => '4.2',
            'is_bulk' => '1',
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
                    'pricesHistory',
                    'isBulk',
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
                    // TODO: implement pricesHistory in app
                    'pricesHistory' => [/*array of prices (raw)*/],
                    'supplier' => [
                        'id' => $supplier->id,
                        'name' => $supplier->name,
                        'description' => $supplier->description,
                        'address' => $supplier->address,
                        'phone' => $supplier->phone,
                        'email' => $supplier->email,
                        'supplierSince' => $supplier->supplier_since,
                    ],
                ]
            ]);
    }

    public function testUpdate1(): void
    {
        $supplier_1 = factory(Supplier::class)->create();
        $supplier_2 = factory(Supplier::class)->create();

        $article = factory(Article::class)->create(['supplier_id' => $supplier_1->id]);
        $id = $article->id;
        $price = factory(Price::class)->make();
        $article->prices()->save($price);
        $article->food()->save(factory(Food::class)->make(['is_bulk' => false]));


        $response = $this->putJson('/api/food/' . $id, [
            'name' => 'Food',
            'quantity' => '42',
            'supplier_id' => $supplier_2->id,
            'unit_price' => '142.42',
            'value' => '4.2',
            'is_bulk' => '1',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'id' => $id,
                    'name' => 'Food',
                    'quantity' => '42',
                    'unitPrice' => '142.42',
                    'price' => [
                        'id' => $price->id + 1,
                        'value' => '4.20',
                    ],
                    // TODO: 'pricesHistory' is present but will need to be defined later, will return true anyway
                    'isBulk' => '1',
                    'supplier' => [
                        'id' => $supplier_2->id,
                        'name' => $supplier_2->name,
                        'description' => $supplier_2->description,
                        'address' => $supplier_2->address,
                        'phone' => $supplier_2->phone,
                        'email' => $supplier_2->email,
                        'supplierSince' => $supplier_2->supplier_since,
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

        $article = factory(Article::class)->create(['supplier_id' => $supplier->id]);
        $id = $article->id;
        $price = factory(Price::class)->make();
        $article->prices()->save($price);
        $article->food()->save(factory(Food::class)->make(['is_bulk' => false]));

        $response = $this->getJson('/api/food/' . $id);

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
                    'pricesHistory',
                    'isBulk',
                    'supplier' => [
                        'id', 'name', 'description',
                        'address', 'phone', 'email', 'supplierSince',
                    ],
                ]
            ])
            ->assertJson([
                'data' => [
                    'id' => $id,
                    'name' => $article->name,
                    'quantity' => $article->quantity,
                    'unitPrice' => $article->unit_price,
                    'price' => [
                        'id' => $price->id,
                        'value' => $price->value,
                    ],
                    // TODO: implement pricesHistory in app
                    'pricesHistory' => [/*array of prices (raw)*/],
                    'isBulk' => false,
                    'supplier' => [
                        'id' => $supplier->id,
                        'name' => $supplier->name,
                        'description' => $supplier->description,
                        'address' => $supplier->address,
                        'phone' => $supplier->phone,
                        'email' => $supplier->email,
                        'supplierSince' => $supplier->supplier_since,
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
        $article = factory(Article::class)->create(['supplier_id' => $supplier->id]);
        $id = $article->id;
        $article->prices()->save(factory(Price::class)->make());
        $article->food()->save(factory(Food::class)->make());

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
