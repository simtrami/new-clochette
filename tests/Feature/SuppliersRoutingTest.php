<?php

namespace Tests\Feature;

use App\Supplier;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SuppliersRoutingTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @return void
     */
    public function testIndex()
    {
        factory(Supplier::class, 2)->create();

        $response = $this->get('/api/suppliers');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    0 => [
                        'id',
                        'name',
                        'description',
                        'address',
                        'phone',
                        'email',
                        'supplierSince'
                    ],
                    1 => [
                        'id',
                        'name',
                        'description',
                        'address',
                        'phone',
                        'email',
                        'supplierSince'
                    ]
                ]
            ]);
    }

    public function testCreate()
    {
        $response = $this->postJson('/api/suppliers', [
            'name' => 'Supplier McTest',
            'description' => "Labore cupiditate doloribus qui laborum. Voluptatibus deleniti facere quasi sit tenetur accusantium magnam. Ab et consequatur itaque nostrum. Sint voluptas similique nihil.",
            'address' => "568 Dane Harbors Apt. 171, Runteburgh, IL 00560",
            'phone' => "762.943.3595 x82638",
            'email' => 'supplier@mctest.com',
            'supplier_since' => '2019-04-01'
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'name',
                    'description',
                    'address',
                    'phone',
                    'email',
                    'supplierSince'
                ]
            ]);
    }

    public function testUpdate()
    {
        $supplier = factory(Supplier::class)->create();

        $response = $this->putJson('/api/suppliers/' . $supplier->id, [
            'name' => 'Supplier McTest',
            'description' => "Labore cupiditate doloribus qui laborum. Voluptatibus deleniti facere quasi sit tenetur accusantium magnam. Ab et consequatur itaque nostrum. Sint voluptas similique nihil.",
            'address' => "568 Dane Harbors Apt. 171, Runteburgh, IL 00560",
            'phone' => "762.943.3595 x82638",
            'email' => 'supplier@mctest.com',
            'supplier_since' => '2019-04-01'
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'id' => $supplier->id,
                    'name' => 'Supplier McTest',
                    'description' => "Labore cupiditate doloribus qui laborum. Voluptatibus deleniti facere quasi sit tenetur accusantium magnam. Ab et consequatur itaque nostrum. Sint voluptas similique nihil.",
                    'address' => "568 Dane Harbors Apt. 171, Runteburgh, IL 00560",
                    'phone' => "762.943.3595 x82638",
                    'email' => 'supplier@mctest.com',
                    'supplierSince' => '2019-04-01',
                    'contacts' => []
                ]
            ]);
    }

    public function testShow()
    {
        $supplier = factory(Supplier::class)->create();

        $response = $this->getJson('/api/suppliers/' . $supplier->id);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'name',
                    'description',
                    'address',
                    'phone',
                    'email',
                    'supplierSince',
                    'contacts'
                ]
            ]);
    }

    public function testDestroy()
    {
        $supplier = factory(Supplier::class)->create();

        $response = $this->deleteJson('/api/suppliers/' . $supplier->id);

        $response->assertStatus(204);
    }
}
