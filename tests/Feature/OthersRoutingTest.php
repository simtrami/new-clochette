<?php

namespace Tests\Feature;

use App\Article;
use App\Item;
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
        $other_1 = factory(Other::class)->make();
        $other_2 = factory(Other::class)->make();

        $item_1 = factory(Item::class)->create();
        $item_2 = factory(Item::class)->create();

        $item_1->prices()->save($price_1);
        $item_1->article()->save($article_1);
        $item_1->article->other()->save($other_1);
        $item_2->prices()->save($price_2);
        $item_2->article()->save($article_2);
        $item_2->article->other()->save($other_2);

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

    public function testCreate()
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
            ]);
    }

    public function testUpdate()
    {

        $supplier_1 = factory(Supplier::class)->create();
        $item = factory(Item::class)->create();
        $id = $item->id;
        $price = factory(Price::class)->create(['item_id' => $id]);
        factory(Article::class)->create(['item_id' => $id, 'supplier_id' => $supplier_1->id]);
        factory(Other::class)->create(['article_id' => $id]);

        $supplier_2 = factory(Supplier::class)->create();

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
//                    'pricesHistory' is present but will need to be defined later, will return true anyway
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

    public function testShow()
    {
        $supplier = factory(Supplier::class)->create();
        $item = factory(Item::class)->create();
        $id = $item->id;
        factory(Price::class)->create(['item_id' => $id]);
        factory(Article::class)->create(['item_id' => $id, 'supplier_id' => $supplier->id]);
        factory(Other::class)->create(['article_id' => $id]);

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
            ]);
    }

    public function testDestroy()
    {
        $supplier = factory(Supplier::class)->create();
        $item = factory(Item::class)->create();
        $id = $item->id;
        factory(Price::class)->create(['item_id' => $id]);
        factory(Article::class)->create(['item_id' => $id, 'supplier_id' => $supplier->id]);
        factory(Other::class)->create(['article_id' => $id]);

        $response = $this->deleteJson('/api/others/' . $id);
        $response->assertStatus(204);

        // Testing soft delete
        $response = $this->deleteJson('/api/others/' . $id);
        $response->assertStatus(404);
    }
}
