<?php

namespace Tests\Feature;

use App\Article;
use App\Barrel;
use App\Bottle;
use App\Food;
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
        $articles = factory(Article::class, 2)
            ->create()
            ->each(function ($article) {
                $article->prices()->save(factory(Price::class)->make());
                $article->barrel()->save(factory(Barrel::class)->make());
            });

        $kits = factory(Kit::class, 2)
            ->create()
            ->each(function ($kit) {
                $kit->prices()->save(factory(Price::class)->make());
            });

        Kit::find($kits[0]->id)->articles()->attach($articles[0]->id, ['article_quantity' => random_int(1, 10)]);

        Kit::find($kits[1]->id)->articles()->attach([
            $articles[0]->id => ['article_quantity' => random_int(1, 10)],
            $articles[1]->id => ['article_quantity' => random_int(1, 10)],
        ]);

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

    public function testUpdate1(): void
    {
        $articles[] = factory(Article::class)->create();
        $articles[0]->prices()->save(factory(Price::class)->make(['value' => 10.6]));
        $articles[0]->barrel()->save(factory(Barrel::class)->make(['volume' => 24]));

        $articles[] = factory(Article::class)->create();
        $articles[1]->prices()->save(factory(Price::class)->make(['value' => 12.26]));
        $articles[1]->bottle()->save(factory(Bottle::class)->make(['volume' => 2]));

        $articles[] = factory(Article::class)->create();
        $articles[2]->prices()->save(factory(Price::class)->make());
        $articles[2]->bottle()->save(factory(Food::class)->make());

        $kit = factory(Kit::class)->create();
        $price = factory(Price::class)->make();
        $kit->prices()->save($price);
        $id = $kit->id;

        Kit::find($id)->articles()->attach($articles[2]->id, ['article_quantity' => 1]);

        $response = $this->putJson('/api/kits/' . $id, [
            'name' => 'Kit',
            'quantity' => '42',
            'value' => '4.2',
            'articles' => [
                0 => [
                    'id' => $articles[0]->id,
                    'quantity' => 4,
                ],
                1 => [
                    'id' => $articles[1]->id,
                    'quantity' => 2,
                ],
            ],
            'detached_articles' => [$articles[2]->id],
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
                            'id' => $articles[0]->id,
                            'name' => $articles[0]->name,
                            'articleQuantity' => 4,
                            'price' => '10.60',
                            'type' => 'barrel',
                            'volume' => '24.00',
                        ],
                        1 => [
                            'id' => $articles[1]->id,
                            'name' => $articles[1]->name,
                            'articleQuantity' => 2,
                            'price' => '12.26',
                            'type' => 'bottle',
                            'volume' => '2.00',
                        ],
                    ],
                ]
            ]);
    }

    public function testUpdate2(): void
    {
        $response = $this->putJson('/api/kits/0');
        $response->assertStatus(404);
    }

    public function testShow1(): void
    {
        $article_1 = factory(Article::class)->create();
        $article_1->prices()->save(factory(Price::class)->make());
        $article_1->barrel()->save(factory(Barrel::class)->make());

        $article_2 = factory(Article::class)->create();
        $article_2->prices()->save(factory(Price::class)->make());
        $article_2->bottle()->save(factory(Bottle::class)->make());

        $kit = factory(Kit::class)->create();
        $kit->prices()->save(factory(Price::class)->make());

        $kit->articles()->attach([
            $article_1->id => ['article_quantity' => random_int(1, 10)],
            $article_2->id => ['article_quantity' => random_int(1, 10)],
        ]);

        $response = $this->getJson('/api/kits/' . $kit->id);

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

    public function testShow2(): void
    {
        $response = $this->getJson('/api/kits/0');
        $response->assertStatus(404);
    }

    public function testDestroy(): void
    {
        $kit = factory(Kit::class)->create();
        $kit->prices()->save(factory(Price::class)->make());

        $response = $this->deleteJson('/api/kits/' . $kit->id);
        $response->assertStatus(204);
        // Check whether the resource is unreachable
        $response = $this->deleteJson('/api/kits/' . $kit->id);
        $response->assertStatus(404);
        $response = $this->putJson('/api/kits/' . $kit->id);
        $response->assertStatus(404);
        $response = $this->getJson('/api/kits/' . $kit->id);
        $response->assertStatus(404);
        // Check the soft delete success
        self::assertNotNull(Kit::onlyTrashed()->find($kit->id));
    }
}
