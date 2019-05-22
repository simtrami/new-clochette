<?php

namespace Tests\Feature;

use App\Article;
use App\Bottle;
use App\Item;
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
    public function testIndex()
    {
        factory(Supplier::class)->create();
        $supplier = Supplier::first();

        $article_1 = factory(Article::class)->make();
        $article_2 = factory(Article::class)->make();

        // Verifying the supplier is not loaded when entities are listed
        $article_1->supplier()->associate($supplier);
        $article_2->supplier()->associate($supplier);

        $price_1 = factory(Price::class)->make();
        $price_2 = factory(Price::class)->make();
        $bottle_1 = factory(Bottle::class)->make();
        $bottle_2 = factory(Bottle::class)->make();

        $item_1 = factory(Item::class)->create();
        $item_2 = factory(Item::class)->create();

        $item_1->prices()->save($price_1);
        $item_1->article()->save($article_1);
        $item_1->article->bottle()->save($bottle_1);
        $item_2->prices()->save($price_2);
        $item_2->article()->save($article_2);
        $item_2->article->bottle()->save($bottle_2);

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
                        'pricesHistory',
                        'volume',
                        'isReturnable',
                    ],
                    1 => [
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
                    ]
                ]
            ]);
    }

    public function testCreate()
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
                    'supplier' => [
                        'id', 'name', 'description',
                        'address', 'phone', 'email', 'supplierSince',
                    ],
                ]
            ]);
    }

    public function testUpdate()
    {

        $supplier_1 = factory(Supplier::class)->create();
        $item = factory(Item::class)->create();
        $id = $item->id;
        $price = factory(Price::class)->create(['item_id' => $id]);
        factory(Article::class)->create(['item_id' => $id, 'supplier_id' => $supplier_1->id]);
        factory(Bottle::class)->create(['article_id' => $id, 'is_returnable' => false]);

        $supplier_2 = factory(Supplier::class)->create();

        $response = $this->putJson('/api/bottles/' . $id, [
            'name' => 'Bottle',
            'quantity' => '42',
            'supplier_id' => $supplier_2->id,
            'unit_price' => '142.42',
            'value' => '4.2',
            'volume' => '30',
            'is_returnable' => '1',
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
//                    'pricesHistory' is present but will need to be defined later, will return true anyway
                    'volume' => '30',
                    'isReturnable' => '1',
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

    public function testShow()
    {
        $supplier = factory(Supplier::class)->create();
        $item = factory(Item::class)->create();
        $id = $item->id;
        factory(Price::class)->create(['item_id' => $id]);
        factory(Article::class)->create(['item_id' => $id, 'supplier_id' => $supplier->id]);
        factory(Bottle::class)->create(['article_id' => $id, 'is_returnable' => false]);

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
                    'supplier' => [
                        'id', 'name', 'description',
                        'address', 'phone', 'email', 'supplierSince',
                    ],
                ]
            ]);
    }

    public function testDestroy()
    {
        $supplier = factory(Supplier::class)->create();
        $item = factory(Item::class)->create();
        $id = $item->id;
        factory(Price::class)->create(['item_id' => $id]);
        factory(Article::class)->create(['item_id' => $id, 'supplier_id' => $supplier->id]);
        factory(Bottle::class)->create(['article_id' => $id]);

        $response = $this->deleteJson('/api/bottles/' . $id);
        $response->assertStatus(204);

        // Testing soft delete
        $response = $this->deleteJson('/api/bottles/' . $id);
        $response->assertStatus(404);
    }
}
