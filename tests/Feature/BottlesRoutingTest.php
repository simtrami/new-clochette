<?php

namespace Tests\Feature;

use App\Article;
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

        factory(Article::class, 2)
            ->create(['supplier_id' => $supplier->id])
            ->each(function ($article) {
                $article->prices()->save(factory(Price::class)->make());
                $article->bottle()->save(factory(Bottle::class)->make());
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
            'quantity' => '42',
            'supplier_id' => $supplier->id,
            'unit_price' => '142.42',
            'value' => '4.2',
            'volume' => '30',
            'is_returnable' => '1',
            'abv' => '3.44',
            'ibu' => '32.4',
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
                    'abv' => 3.44,
                    'ibu' => 32.4,
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
        $article->bottle()->save(factory(Bottle::class)->make(['is_returnable' => false]));


        $response = $this->putJson('/api/bottles/' . $id, [
            'name' => 'Bottle',
            'quantity' => '42',
            'supplier_id' => $supplier_2->id,
            'unit_price' => '142.42',
            'value' => '4.2',
            'volume' => '30',
            'is_returnable' => '1',
            'abv' => '3.44',
            'ibu' => '32.4',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'id' => $id,
                    'name' => 'Bottle',
                    'quantity' => '42',
                    'unitPrice' => '142.42',
                    'price' => [
                        'id' => $price->id + 1,
                        'value' => '4.20',
                    ],
                    // TODO: 'pricesHistory' is present but will need to be defined later, will return true anyway
                    'volume' => '30',
                    'isReturnable' => '1',
                    'abv' => 3.44,
                    'ibu' => 32.4,
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
        $response = $this->putJson('/api/bottles/0');
        $response->assertStatus(404);
    }

    public function testShow1(): void
    {
        $supplier = factory(Supplier::class)->create();

        $article = factory(Article::class)->create(['supplier_id' => $supplier->id]);
        $id = $article->id;
        $price = factory(Price::class)->make();
        $article->prices()->save($price);
        $article->bottle()->save(factory(Bottle::class)->make(['is_returnable' => false]));

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
                    'pricesHistory',
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
                    'name' => $article->name,
                    'quantity' => $article->quantity,
                    'unitPrice' => $article->unit_price,
                    'price' => [
                        'id' => $price->id,
                        'value' => $price->value,
                    ],
                    // TODO: implement pricesHistory in app
                    'pricesHistory' => [/*array of prices (raw)*/],
                    'volume' => $article->bottle->volume,
                    'isReturnable' => false,
                    'abv' => $article->bottle->abv,
                    'ibu' => $article->bottle->ibu,
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
        $response = $this->getJson('/api/bottles/0');
        $response->assertStatus(404);
    }

    public function testDestroy(): void
    {
        $supplier = factory(Supplier::class)->create();
        $article = factory(Article::class)->create(['supplier_id' => $supplier->id]);
        $id = $article->id;
        $article->prices()->save(factory(Price::class)->make());
        $article->bottle()->save(factory(Bottle::class)->make());

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
