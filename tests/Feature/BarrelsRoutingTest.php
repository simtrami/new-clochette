<?php

namespace Tests\Feature;

use App\Article;
use App\Barrel;
use App\Item;
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
    public function testIndex()
    {
        $supplier = factory(Supplier::class)->create();

        $item_1 = factory(Item::class)->create();
        factory(Price::class)->create(['item_id' => $item_1->id, 'second_value' => '4.2']);
        factory(Article::class)->create(['item_id' => $item_1->id, 'supplier_id' => $supplier->id]);
        factory(Barrel::class)->create(['article_id' => $item_1->id]);

        $item_2 = factory(Item::class)->create();
        factory(Price::class)->create(['item_id' => $item_2->id, 'second_value' => '2.4']);
        factory(Article::class)->create(['item_id' => $item_2->id, 'supplier_id' => $supplier->id]);
        factory(Barrel::class)->create(['article_id' => $item_2->id]);

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
                        'withdrawalType',
                        'abv', 'ibu', 'variety',
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
                        'withdrawalType',
                        'abv', 'ibu', 'variety',
                    ]
                ]
            ]);
    }

    public function testCreate()
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
            'withdrawal_type' => 'KeyKeg',
            'abv' => '4.5',
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
                    'withdrawalType',
                    'abv', 'ibu', 'variety',
                    'supplier' => [
                        'id', 'name', 'description', 'address', 'phone', 'email', 'supplierSince',
                    ],
                ]
            ]);
    }

    public function testUpdate()
    {
        $supplier_1 = factory(Supplier::class)->create();
        $item = factory(Item::class)->create();
        $id = $item->id;
        $price = factory(Price::class)->create(['item_id' => $id, 'second_value' => '3.4']);
        factory(Article::class)->create(['item_id' => $id, 'supplier_id' => $supplier_1->id]);
        factory(Barrel::class)->create(['article_id' => $id]);

        $supplier_2 = factory(Supplier::class)->create();

        $response = $this->putJson('/api/barrels/' . $id, [
            'name' => 'Barrel',
            'quantity' => '42',
            'supplier_id' => $supplier_2->id,
            'unit_price' => '142.42',
            'value' => '4.2',
            'second_value' => '2.6',
            'volume' => '30',
            'withdrawal_type' => 'KeyKeg',
            'abv' => '4.5',
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
//                    'pricesHistory' is present but will need to be defined later, will return true anyway
                    'volume' => '30',
                    'withdrawalType' => 'KeyKeg',
                    'abv' => '4.50',
                    'ibu' => null,
                    'variety' => null,
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
        factory(Price::class)->create(['item_id' => $id, 'second_value' => '3.4']);
        factory(Article::class)->create(['item_id' => $id, 'supplier_id' => $supplier->id]);
        factory(Barrel::class)->create(['article_id' => $id]);

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
                    'withdrawalType',
                    'abv', 'ibu', 'variety',
                    'supplier' => [
                        'id', 'name', 'description', 'address', 'phone', 'email', 'supplierSince',
                    ],
                ]
            ]);
    }

    public function testDestroy()
    {
        $supplier = factory(Supplier::class)->create();
        $item = factory(Item::class)->create();
        $id = $item->id;
        factory(Price::class)->create(['item_id' => $id, 'second_value' => '3.4']);
        factory(Article::class)->create(['item_id' => $id, 'supplier_id' => $supplier->id]);
        factory(Barrel::class)->create(['article_id' => $id]);

        $response = $this->deleteJson('/api/barrels/' . $id);
        $response->assertStatus(204);

        // Testing soft delete
        $response = $this->deleteJson('/api/barrels/' . $id);
        $response->assertStatus(404);
    }
}
