<?php

namespace Tests\Feature;

use App\Models\Barrel;
use App\Models\Contact;
use App\Models\Supplier;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SuppliersRoutingTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @return void
     */
    public function testIndex(): void
    {
        Supplier::factory()->count(2)->create();

        $response = $this->get('/api/suppliers');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    0 => [
                        'id',
                        'name',
                        'notes',
                        'address',
                        'phone',
                        'email',
                        'supplierSince'
                    ],
                    1 => [
                        'id',
                        'name',
                        'notes',
                        'address',
                        'phone',
                        'email',
                        'supplierSince'
                    ]
                ]
            ]);
    }

    public function testCreate(): void
    {
        $response = $this->postJson('/api/suppliers', [
            'name' => 'Supplier McTest',
            'notes' => "Labore cupiditate doloribus qui laborum. Voluptatibus deleniti facere quasi sit tenetur accusantium magnam. Ab et consequatur itaque nostrum. Sint voluptas similique nihil.",
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
                    'notes',
                    'address',
                    'phone',
                    'email',
                    'supplierSince'
                ]
            ]);
    }

    public function testUpdate1(): void
    {
        $supplier = Supplier::factory()->create();

        $response = $this->putJson('/api/suppliers/' . $supplier->id, [
            'name' => 'Supplier McTest',
            'notes' => "Labore cupiditate doloribus qui laborum. Voluptatibus deleniti facere quasi sit tenetur accusantium magnam. Ab et consequatur itaque nostrum. Sint voluptas similique nihil.",
            'address' => "568 Dane Harbors Apt. 171, Runteburgh, IL 00560",
            'phone' => "762.943.3595 x82638",
            'email' => 'supplier@mctest.com',
            'supplier_since' => '2019-04-01'
        ]);

        $supplier_since = new Carbon('2019-04-01');

        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'id' => $supplier->id,
                    'name' => 'Supplier McTest',
                    'notes' => "Labore cupiditate doloribus qui laborum. Voluptatibus deleniti facere quasi sit tenetur accusantium magnam. Ab et consequatur itaque nostrum. Sint voluptas similique nihil.",
                    'address' => "568 Dane Harbors Apt. 171, Runteburgh, IL 00560",
                    'phone' => "762.943.3595 x82638",
                    'email' => 'supplier@mctest.com',
                    'supplierSince' => $supplier_since->toISOString(),
                    'contacts' => [],
                ]
            ]);
    }

    public function testUpdate2(): void
    {
        $response = $this->putJson('/api/suppliers/0');
        $response->assertStatus(404);
    }

    public function testShow1(): void
    {
        $supplier = Supplier::factory()->create();

        $response = $this->getJson('/api/suppliers/' . $supplier->id);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'name',
                    'notes',
                    'address',
                    'phone',
                    'email',
                    'supplierSince',
                    'contacts'
                ]
            ]);
    }

    public function testShow2(): void
    {
        $response = $this->getJson('/api/suppliers/0');
        $response->assertStatus(404);
    }

    public function testDestroy1(): void
    {
        $supplier = Supplier::factory()->create();

        $response = $this->deleteJson('/api/suppliers/' . $supplier->id);
        $response->assertStatus(204);
        // Check whether the resource is unreachable
        $response = $this->deleteJson('/api/suppliers/' . $supplier->id);
        $response->assertStatus(404);
        $response = $this->putJson('/api/suppliers/' . $supplier->id);
        $response->assertStatus(404);
        $response = $this->getJson('/api/suppliers/' . $supplier->id);
        $response->assertStatus(404);
        // Check the destruction in database
        self::assertNull(Supplier::find($supplier->id));
    }

    public function testDestroy2(): void
    {
        $supplier = Supplier::factory()->create();
        $contact = Contact::factory()->create(['supplier_id' => $supplier->id]);

        self::assertEquals(1, Contact::whereSupplierId($supplier->id)->count());

        $response = $this->deleteJson('/api/suppliers/' . $supplier->id);
        $response->assertStatus(204);
        // Check the 'ON DELETE CASCADE' behaviour of contacts
        self::assertEquals(0, Contact::whereSupplierId($supplier->id)->count());
        self::assertNull(Contact::find($contact->id));
    }

    public function testDestroy3(): void
    {
        $supplier = Supplier::factory()->create();
        $barrel = Barrel::factory()->create(['supplier_id' => $supplier->id]);

        self::assertEquals(1, Barrel::whereSupplierId($supplier->id)->count());

        $response = $this->deleteJson('/api/suppliers/' . $supplier->id);
        $response->assertStatus(204);
        // Check the 'ON DELETE SET NULL' behaviour of article's supplier_id
        self::assertEquals(0, Barrel::whereSupplierId($supplier->id)->count());
        self::assertNull(Barrel::find($barrel->id)->supplier_id);
    }
}
