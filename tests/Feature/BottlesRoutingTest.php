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
    public function testIndex(): void
    {
        $supplier = factory(Supplier::class)->create();

        $item_1 = factory(Item::class)->create();
        factory(Price::class)->create(['item_id' => $item_1->id]);
        factory(Article::class)->create(['id' => $item_1->id, 'supplier_id' => $supplier->id]);
        factory(Bottle::class)->create(['id' => $item_1->id]);

        $item_2 = factory(Item::class)->create();
        factory(Price::class)->create(['item_id' => $item_2->id]);
        factory(Article::class)->create(['id' => $item_2->id, 'supplier_id' => $supplier->id]);
        factory(Bottle::class)->create(['id' => $item_2->id]);

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
                        'abv', 'ibu', 'variety',
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
                        'abv', 'ibu', 'variety',
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
            'abv' => '3.4',
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
                    'abv', 'ibu', 'variety',
                    'supplier' => [
                        'id', 'name', 'description',
                        'address', 'phone', 'email', 'supplierSince',
                    ],
                ]
            ]);
    }

    public function testUpdate(): void
    {

        $supplier_1 = factory(Supplier::class)->create();
        $item = factory(Item::class)->create();
        $id = $item->id;
        $price = factory(Price::class)->create(['item_id' => $id]);
        factory(Article::class)->create(['id' => $id, 'supplier_id' => $supplier_1->id]);
        factory(Bottle::class)->create(['id' => $id, 'is_returnable' => false]);

        $supplier_2 = factory(Supplier::class)->create();

        $response = $this->putJson('/api/bottles/' . $id, [
            'name' => 'Bottle',
            'quantity' => '42',
            'supplier_id' => $supplier_2->id,
            'unit_price' => '142.42',
            'value' => '4.2',
            'volume' => '30',
            'is_returnable' => '1',
            'abv' => '3.4',
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
                    'abv' => '3.40',
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

    public function testShow(): void
    {
        $supplier = factory(Supplier::class)->create();
        $item = factory(Item::class)->create();
        $id = $item->id;
        factory(Price::class)->create(['item_id' => $id]);
        factory(Article::class)->create(['id' => $id, 'supplier_id' => $supplier->id]);
        factory(Bottle::class)->create(['id' => $id, 'is_returnable' => false]);

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
                    'abv', 'ibu', 'variety',
                    'supplier' => [
                        'id', 'name', 'description',
                        'address', 'phone', 'email', 'supplierSince',
                    ],
                ]
            ]);
    }

    public function testDestroy(): void
    {
        $supplier = factory(Supplier::class)->create();
        $item = factory(Item::class)->create();
        $id = $item->id;
        factory(Price::class)->create(['item_id' => $id]);
        factory(Article::class)->create(['id' => $id, 'supplier_id' => $supplier->id]);
        factory(Bottle::class)->create(['id' => $id]);

        $response = $this->deleteJson('/api/bottles/' . $id);
        $response->assertStatus(204);

        // Testing soft delete
        $response = $this->deleteJson('/api/bottles/' . $id);
        $response->assertStatus(404);
    }
}
