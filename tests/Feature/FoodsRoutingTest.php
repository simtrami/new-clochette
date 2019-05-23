<?php

namespace Tests\Feature;

use App\Article;
use App\Food;
use App\Item;
use App\Price;
use App\Supplier;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FoodsRoutingTest extends TestCase
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
        $food_1 = factory(Food::class)->make(['is_bulk' => true, 'units_left' => 50]);
        $food_2 = factory(Food::class)->make(['is_bulk' => false, 'units_left' => null]);

        $item_1 = factory(Item::class)->create();
        $item_2 = factory(Item::class)->create();

        $item_1->prices()->save($price_1);
        $item_1->article()->save($article_1);
        $item_1->article->food()->save($food_1);
        $item_2->prices()->save($price_2);
        $item_2->article()->save($article_2);
        $item_2->article->food()->save($food_2);

        $response = $this->get('/api/foods');

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
                        'unitsLeft',
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

    public function testCreate()
    {
        $supplier = factory(Supplier::class)->create();

        $response = $this->postJson('/api/foods', [
            'name' => 'Food',
            'quantity' => '42',
            'supplier_id' => $supplier->id,
            'unit_price' => '142.42',
            'value' => '4.2',
            'is_bulk' => '1',
            'units_left' => '30',
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
                    'unitsLeft',
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
        factory(Food::class)->create(['article_id' => $id, 'is_bulk' => false, 'units_left' => null]);

        $supplier_2 = factory(Supplier::class)->create();

        $response = $this->putJson('/api/foods/' . $id, [
            'name' => 'Food',
            'quantity' => '42',
            'supplier_id' => $supplier_2->id,
            'unit_price' => '142.42',
            'value' => '4.2',
            'is_bulk' => '1',
            'units_left' => '30',
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
//                    'pricesHistory' is present but will need to be defined later, will return true anyway
                    'isBulk' => '1',
                    'unitsLeft' => '30',
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
        factory(Food::class)->create(['article_id' => $id, 'is_bulk' => false, 'units_left' => null]);

        $response = $this->getJson('/api/foods/' . $id);

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
            ]);
    }

    public function testDestroy()
    {
        $supplier = factory(Supplier::class)->create();
        $item = factory(Item::class)->create();
        $id = $item->id;
        factory(Price::class)->create(['item_id' => $id]);
        factory(Article::class)->create(['item_id' => $id, 'supplier_id' => $supplier->id]);
        factory(Food::class)->create(['article_id' => $id]);

        $response = $this->deleteJson('/api/foods/' . $id);
        $response->assertStatus(204);

        // Testing soft delete
        $response = $this->deleteJson('/api/foods/' . $id);
        $response->assertStatus(404);
    }
}
