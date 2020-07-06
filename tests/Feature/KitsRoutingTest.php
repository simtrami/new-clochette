<?php

namespace Tests\Feature;

use App\Article;
use App\Barrel;
use App\Bottle;
use App\Food;
use App\Item;
use App\Kit;
use App\Price;
use Exception;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class KitsRoutingTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @return void
     * @throws Exception
     */
    public function testIndex(): void
    {
        $item_1 = factory(Item::class)->create();
        factory(Price::class)->create(['item_id' => $item_1->id]);
        factory(Article::class)->create(['id' => $item_1->id]);
        factory(Barrel::class)->create(['id' => $item_1->id]);

        $item_2 = factory(Item::class)->create();
        factory(Price::class)->create(['item_id' => $item_2->id]);
        factory(Article::class)->create(['id' => $item_2->id]);
        factory(Bottle::class)->create(['id' => $item_2->id]);

        $item_3 = factory(Item::class)->create();
        factory(Price::class)->create(['item_id' => $item_3->id]);
        factory(Kit::class)->create(['id' => $item_3->id]);
        Kit::find($item_3->id)->articles()->attach($item_1->id, ['article_quantity' => random_int(1, 10)]);

        $item_4 = factory(Item::class)->create();
        factory(Price::class)->create(['item_id' => $item_4->id]);
        factory(Kit::class)->create(['id' => $item_4->id]);
        Kit::find($item_4->id)->articles()->attach($item_1->id, ['article_quantity' => random_int(1, 10)]);
        Kit::find($item_4->id)->articles()->attach($item_2->id, ['article_quantity' => random_int(1, 10)]);

        $response = $this->get('/api/kits');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    0 => [
                        'id',
                        'name',
                        'quantity',
                        'price' => [
                            'id', 'value',
                        ],
                        'nbArticles',
                    ],
                    1 => [
                        'id',
                        'name',
                        'quantity',
                        'price' => [
                            'id', 'value',
                        ],
                        'nbArticles',
                    ]
                ]
            ]);
    }

    public function testCreate(): void
    {
        $response = $this->postJson('/api/kits', [
            'name' => 'Kit',
            'quantity' => '42',
            'value' => '14.2',
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'name',
                    'quantity',
                    'price' => [
                        'id', 'value',
                    ],
                    'pricesHistory',
                    'articles',
                ]
            ]);
    }

    public function testUpdate(): void
    {
        $item_1 = factory(Item::class)->create();
        factory(Price::class)->create(['item_id' => $item_1->id, 'value' => 10.6]);
        factory(Article::class)->create(['id' => $item_1->id]);
        factory(Barrel::class)->create(['id' => $item_1->id, 'volume' => 24]);

        $item_2 = factory(Item::class)->create();
        factory(Price::class)->create(['item_id' => $item_2->id, 'value' => 12.26]);
        factory(Article::class)->create(['id' => $item_2->id]);
        factory(Bottle::class)->create(['id' => $item_2->id, 'volume' => 2]);

        $item_3 = factory(Item::class)->create();
        factory(Price::class)->create(['item_id' => $item_3->id]);
        factory(Article::class)->create(['id' => $item_3->id]);
        factory(Food::class)->create(['id' => $item_3->id]);

        $item_4 = factory(Item::class)->create();
        $price = factory(Price::class)->create(['item_id' => $item_4->id]);
        factory(Kit::class)->create(['id' => $item_4->id]);

        try {
            Kit::find($item_4->id)->articles()->attach($item_3->id, ['article_quantity' => 1]);
        } catch (Exception $e) {
            throwException($e);
        }

        $id = $item_4->id;

        $response = $this->putJson('/api/kits/' . $id, [
            'name' => 'Kit',
            'quantity' => '42',
            'value' => '4.2',
            'articles' => [
                0 => [
                    'id' => $item_1->id,
                    'quantity' => 4,
                ],
                1 => [
                    'id' => $item_2->id,
                    'quantity' => 2,
                ],
            ],
            'detached_articles' => [$item_3->id],
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'id' => $id,
                    'name' => 'Kit',
                    'quantity' => '42',
                    // TODO: 'pricesHistory' is present but will need to be defined later, will return true anyway
                    'price' => [
                        'id' => $price->id + 1,
                        'value' => '4.20',
                    ],
                    'articles' => [
                        0 => [
                            'id' => $item_1->id,
                            'name' => $item_1->name,
                            'articleQuantity' => 4,
                            'price' => '10.60',
                            'type' => 'barrel',
                            'volume' => '24.00',
                        ],
                        1 => [
                            'id' => $item_2->id,
                            'name' => $item_2->name,
                            'articleQuantity' => 2,
                            'price' => '12.26',
                            'type' => 'bottle',
                            'volume' => '2.00',
                        ],
                    ],
                ]
            ]);
    }

    public function testShow(): void
    {
        $item_1 = factory(Item::class)->create();
        factory(Price::class)->create(['item_id' => $item_1->id]);
        factory(Article::class)->create(['id' => $item_1->id]);
        factory(Barrel::class)->create(['id' => $item_1->id]);

        $item_2 = factory(Item::class)->create();
        factory(Price::class)->create(['item_id' => $item_2->id]);
        factory(Article::class)->create(['id' => $item_2->id]);
        factory(Bottle::class)->create(['id' => $item_2->id]);

        $item_3 = factory(Item::class)->create();
        factory(Price::class)->create(['item_id' => $item_3->id]);
        factory(Kit::class)->create(['id' => $item_3->id]);
        try {
            Kit::find($item_3->id)->articles()->attach($item_1->id, ['article_quantity' => random_int(1, 10)]);
        } catch (Exception $e) {
            throwException($e);
        }
        try {
            Kit::find($item_3->id)->articles()->attach($item_2->id, ['article_quantity' => random_int(1, 10)]);
        } catch (Exception $e) {
            throwException($e);
        }

        $response = $this->getJson('/api/kits/' . $item_3->id);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'name',
                    'quantity',
                    'pricesHistory',
                    'price' => [
                        'id', 'value',
                    ],
                    'articles' => [
                        0 => [
                            'id', 'name', 'articleQuantity', 'price', 'type', 'volume',
                        ],
                        1 => [
                            'id', 'name', 'articleQuantity', 'price', 'type', 'volume',
                        ],
                    ],
                ]
            ]);
    }

    public function testDestroy(): void
    {
        $item = factory(Item::class)->create();
        $id = $item->id;
        factory(Price::class)->create(['item_id' => $id]);
        factory(Kit::class)->create(['id' => $id]);

        $response = $this->deleteJson('/api/kits/' . $id);
        $response->assertStatus(204);

        // Testing soft delete
        $response = $this->deleteJson('/api/kits/' . $id);
        $response->assertStatus(404);
    }
}
