<?php

namespace Tests\Feature;

use App\Barrel;
use App\Bottle;
use App\Bundle;
use App\Food;
use App\Price;
use Exception;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BundlesRoutingTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @return void
     * @throws Exception
     */
    public function testIndex(): void
    {
        Bundle::factory()->count(2)
            ->hasAttached(
                Barrel::factory()->hasPrices(),
                ['quantity' => random_int(1, 10)]
            )
            ->hasPrices()
            ->create();

        $response = $this->get('/api/bundles');

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
                    ],
                    1 => [
                        'id',
                        'name',
                        'quantity',
                        'price' => [
                            'id', 'value',
                        ],
                    ]
                ]
            ]);
    }

    public function testCreate(): void
    {
        $barrel = Barrel::factory()
            ->hasPrices(1, ['value' => 4])
            ->create();
        $food = Food::factory()
            ->hasPrices(1, ['value' => 3.5])
            ->create();

        $response = $this->postJson('/api/bundles', [
            'name' => 'Bundle',
            'quantity' => 42,
            'value' => 14.2,
            'barrels' => [
                [
                    'id' => $barrel->id,
                    'quantity' => 1,
                ],
            ],
            'food' => [
                [
                    'id' => $food->id,
                    'quantity' => 2,
                ],
            ],
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
                    'articles',
                ]
            ])
            ->assertJson([
                'data' => [
                    'name' => 'Bundle',
                    'quantity' => 42,
                    'price' => [
                        'value' => 14.2,
                    ],
                    'articles' => [
                        [
                            'id' => $barrel->id,
                            'type' => 'App\Barrel',
                            'name' => $barrel->name,
                            'quantity' => 1,
                        ],
                        [
                            'id' => $food->id,
                            'type' => 'App\Food',
                            'name' => $food->name,
                            'quantity' => 2,
                        ],
                    ],
                ]
            ]);
    }

    public function testUpdate1(): void
    {
        $barrel = Barrel::factory()
            ->hasPrices(1, ['value' => 4])
            ->create();
        $food = Food::factory()
            ->hasPrices(1, ['value' => 6])
            ->create();

        $bottle = Bottle::factory()
            ->hasPrices(1, ['value' => 3.5])
            ->create();

        $bundle = Bundle::factory()->create();
        $price = Price::factory()->make();
        $bundle->prices()->save($price);
        $bundle->barrels()->attach($barrel->id, ['quantity' => 2]);
        $bundle->food()->attach($food->id, ['quantity' => 1]);


        $response = $this->putJson('/api/bundles/' . $bundle->id, [
            'name' => 'Bundle',
            'quantity' => 42,
            'value' => 4.2,
            'barrels' => [
                [
                    'id' => $barrel->id,
                    'quantity' => 1,
                ],
            ],
            'bottles' => [
                [
                    'id' => $bottle->id,
                    'quantity' => 2,
                ],
            ],
            'detached_food' => [$food->id],
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'name',
                    'quantity',
                    'price' => [
                        'id', 'value',
                    ],
                    'priceHistory' => [
                        ['id', 'value', 'createdAt'],
                        ['id', 'value', 'createdAt'],
                    ],
                    'articles',
                ]
            ])
            ->assertJson([
                'data' => [
                    'id' => $bundle->id,
                    'name' => 'Bundle',
                    'quantity' => 42,
                    'price' => [
                        'id' => $price->id + 1,
                        'value' => 4.2,
                    ],
                    'priceHistory' => [
                        [
                            'id' => $price->id,
                            'value' => $price->value,
                            'createdAt' => $price->created_at->toISOString(),
                        ],
                        [
                            'id' => $price->id + 1,
                            'value' => 4.2,
                            'createdAt' => $price->created_at->toISOString(),
                        ],
                    ],
                    'articles' => [
                        [
                            'id' => $barrel->id,
                            'type' => 'App\Barrel',
                            'name' => $barrel->name,
                            'quantity' => 1,
                        ],
                        [
                            'id' => $bottle->id,
                            'type' => 'App\Bottle',
                            'name' => $bottle->name,
                            'quantity' => 2,
                        ],
                    ],
                ]]);
    }

    public function testUpdate2(): void
    {
        $response = $this->putJson('/api/bundles/0');
        $response->assertStatus(404);
    }

    public function testShow1(): void
    {
        $barrel = Barrel::factory()
            ->hasPrices(1)
            ->create();
        $bottle = Bottle::factory()
            ->hasPrices(1)
            ->create();

        $bundle = Bundle::factory()->create();
        $price = Price::factory()->make();
        $bundle->prices()->save($price);

        $bundle->barrels()->attach($barrel->id, ['quantity' => 1]);
        $bundle->bottles()->attach($bottle->id, ['quantity' => 10]);

        $response = $this->getJson('/api/bundles/' . $bundle->id);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'name',
                    'quantity',
                    'price' => [
                        'id', 'value',
                    ],
                    'priceHistory' => [
                        ['id', 'value', 'secondValue', 'createdAt'],
                    ],
                    'articles',
                ]
            ])
            ->assertJson([
                'data' => [
                    'id' => $bundle->id,
                    'name' => $bundle->name,
                    'quantity' => $bundle->quantity,
                    'price' => [
                        'id' => $price->id,
                        'value' => $price->value,
                    ],
                    'priceHistory' => [
                        [
                            'id' => $price->id,
                            'value' => $price->value,
                            'createdAt' => $price->created_at->toISOString(),
                        ],
                    ],
                    'articles' => [
                        [
                            'id' => $barrel->id,
                            'type' => 'App\Barrel',
                            'name' => $barrel->name,
                            'quantity' => 1,
                        ],
                        [
                            'id' => $bottle->id,
                            'type' => 'App\Bottle',
                            'name' => $bottle->name,
                            'quantity' => 10,
                        ],
                    ],
                ]
            ]);
    }

    public function testShow2(): void
    {
        $response = $this->getJson('/api/bundles/0');
        $response->assertStatus(404);
    }

    public function testDestroy(): void
    {
        $bundle = Bundle::factory()
            ->hasPrices(1)
            ->create();

        $response = $this->deleteJson('/api/bundles/' . $bundle->id);
        $response->assertStatus(204);
        // Check whether the resource is unreachable
        $response = $this->deleteJson('/api/bundles/' . $bundle->id);
        $response->assertStatus(404);
        $response = $this->putJson('/api/bundles/' . $bundle->id);
        $response->assertStatus(404);
        $response = $this->getJson('/api/bundles/' . $bundle->id);
        $response->assertStatus(404);
        // Check the soft delete success
        self::assertNotNull(Bundle::onlyTrashed()->find($bundle->id));
    }
}
