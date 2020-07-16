<?php

namespace Tests\Feature;

use App\Article;
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

        $articles = factory(Article::class, 2)
            ->create(['supplier_id' => $supplier->id])
            ->each(function ($article) {
                $article->prices()->save(factory(Price::class)->make());
                $article->other()->save(factory(Other::class)->make());
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
                        'price' => [
                            'id', 'value',
                        ],
                        'description',
                    ],
                    1 => [
                        'id',
                        'name',
                        'quantity',
                        'unitPrice',
                        'price' => [
                            'id', 'value',
                        ],
                        'description',
                    ]
                ]
            ]);
    }

    public function testCreate(): void
    {
        $supplier = factory(Supplier::class)->create();

        $response = $this->postJson('/api/others', [
            'name' => 'Other',
            'quantity' => '42',
            'supplier_id' => $supplier->id,
            'unit_price' => '142.42',
            'value' => '4.2',
            'description' => 'I am another thing you can buy.',
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
                    'description',
                    'supplier' => [
                        'id', 'name', 'description',
                        'address', 'phone', 'email', 'supplierSince',
                    ],
                ]
            ])
            ->assertJson([
                'data' => [
                    'name' => 'Other',
                    'quantity' => '42',
                    'unitPrice' => '142.42',
                    'price' => [
                        'value' => 4.2,
                    ],
                    // TODO: implement pricesHistory in app
                    'pricesHistory' => [/*array of prices (raw)*/],
                    'description' => 'I am another thing you can buy.',
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
        $article->other()->save(factory(Other::class)->make());

        $response = $this->putJson('/api/others/' . $id, [
            'name' => 'Other',
            'quantity' => '42',
            'supplier_id' => $supplier_2->id,
            'unit_price' => '142.42',
            'value' => '4.2',
            'description' => 'I am another thing you can buy.',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'id' => $id,
                    'name' => 'Other',
                    'quantity' => '42',
                    'unitPrice' => '142.42',
                    'price' => [
                        'id' => $price->id + 1,
                        'value' => '4.20',
                    ],
                    // TODO: 'pricesHistory' is present but will need to be defined later, will return true anyway
                    'description' => 'I am another thing you can buy.',
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
        $response = $this->putJson('/api/others/0');
        $response->assertStatus(404);
    }

    public function testShow1(): void
    {
        $supplier = factory(Supplier::class)->create();

        $article = factory(Article::class)->create(['supplier_id' => $supplier->id]);
        $id = $article->id;
        $price = factory(Price::class)->make();
        $article->prices()->save($price);
        $article->other()->save(factory(Other::class)->make());

        $response = $this->getJson('/api/others/' . $id);

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
                    'description',
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
                    'description' => $article->other->description,
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
        $response = $this->getJson('/api/others/0');
        $response->assertStatus(404);
    }

    public function testDestroy(): void
    {
        $supplier = factory(Supplier::class)->create();
        $article = factory(Article::class)->create(['supplier_id' => $supplier->id]);
        $id = $article->id;
        $article->prices()->save(factory(Price::class)->make());
        $article->other()->save(factory(Other::class)->make());

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
