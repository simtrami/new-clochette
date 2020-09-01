<?php

namespace Tests\Feature;

use App\Article;
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

        $article_1 = factory(Article::class)->create(['supplier_id' => $supplier->id]);
        $article_1->prices()->save(factory(Price::class)->make(['second_value' => '4.2']));
        $article_1->barrel()->save(factory(Barrel::class)->make());

        $article_2 = factory(Article::class)->create(['supplier_id' => $supplier->id]);
        $article_2->prices()->save(factory(Price::class)->make());
        $article_2->barrel()->save(factory(Barrel::class)->make());

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
            'quantity' => '42',
            'supplier_id' => $supplier->id,
            'unit_price' => '142.42',
            'value' => '4.2',
            'second_value' => '2.6',
            'volume' => '30',
            'coupler' => 'KeyKeg',
            'abv' => '4.55',
            'ibu' => '42.5',
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
                    'pricesHistory',
                    'volume',
                    'coupler',
                    'abv', 'ibu',
                    'supplier' => [
                        'id', 'name', 'description', 'address', 'phone', 'email', 'supplierSince',
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
        $price = factory(Price::class)->make(['second_value' => '3.4']);
        $article->prices()->save($price);
        $article->barrel()->save(factory(Barrel::class)->make());


        $response = $this->putJson('/api/barrels/' . $id, [
            'name' => 'Barrel',
            'quantity' => '42',
            'supplier_id' => $supplier_2->id,
            'unit_price' => '142.42',
            'value' => '4.2',
            'second_value' => '2.6',
            'volume' => '30',
            'coupler' => 'KeyKeg',
            'abv' => '4.55',
            'ibu' => '42.5',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'id' => $id,
                    'name' => 'Barrel',
                    'quantity' => '42',
                    'unitPrice' => '142.42',
                    'price' => [
                        'id' => $price->id + 1,
                        'value' => '4.20',
                        'secondValue' => '2.60',
                    ],
                    // TODO: 'pricesHistory' is present but will need to be defined later, will return true anyway
                    'volume' => '30',
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
                        'supplierSince' => $supplier_2->supplier_since,
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
        $article = factory(Article::class)->create(['supplier_id' => $supplier->id]);
        $id = $article->id;
        $price = factory(Price::class)->make(['second_value' => '3.4']);
        $article->prices()->save($price);
        $article->barrel()->save(factory(Barrel::class)->make());

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
                    'pricesHistory',
                    'volume',
                    'coupler',
                    'abv', 'ibu',
                    'supplier' => [
                        'id', 'name', 'description', 'address', 'phone', 'email', 'supplierSince',
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
                        'secondValue' => $price->second_value,
                    ],
                    // TODO: implement pricesHistory in app
                    'pricesHistory' => [/*array of prices (raw)*/],
                    'volume' => $article->barrel->volume,
                    'coupler' => $article->barrel->coupler,
                    'abv' => $article->barrel->abv,
                    'ibu' => $article->barrel->ibu,
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
        $response = $this->getJson('/api/barrels/0');
        $response->assertStatus(404);
    }

    public function testDestroy(): void
    {
        $supplier = factory(Supplier::class)->create();
        $article = factory(Article::class)->create(['supplier_id' => $supplier->id]);
        $id = $article->id;
        $article->prices()->save(factory(Price::class)->make(['second_value' => '3.4']));
        $article->barrel()->save(factory(Barrel::class)->make());

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
