<?php

namespace Tests\Feature;

use App\Contact;
use App\Supplier;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ContactsRoutingTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @return void
     */
    public function testIndex(): void
    {
        $supplier = factory(Supplier::class)->create();
        factory(Contact::class, 2)->create(['supplier_id' => $supplier->id]);

        $response = $this->get('/api/contacts');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    0 => [
                        'id',
                        'firstName',
                        'lastName',
                        'phone',
                        'email',
                        'role',
                        'notes'
                    ],
                    1 => [
                        'id',
                        'firstName',
                        'lastName',
                        'phone',
                        'email',
                        'role',
                        'notes'
                    ]
                ]
            ]);
    }

    public function testCreate(): void
    {
        $supplier = factory(Supplier::class)->create();

        $response = $this->postJson('/api/contacts', [
            'supplier_id' => $supplier->id,
            'first_name' => 'Contact',
            'last_name' => 'McTest',
            'phone' => "762.943.3595 x82638",
            'email' => 'contact@mctest.com',
            'role' => 'Contact',
            'notes' => "Labore cupiditate doloribus qui laborum. Voluptatibus deleniti facere quasi sit tenetur accusantium magnam. Ab et consequatur itaque nostrum. Sint voluptas similique nihil."
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'supplier',
                    'firstName',
                    'lastName',
                    'phone',
                    'email',
                    'role',
                    'notes'
                ]
            ]);
    }

    public function testUpdate1(): void
    {
        $supplier1 = factory(Supplier::class)->create();
        $contact = factory(Contact::class)->create(['supplier_id' => $supplier1->id]);

        $supplier2 = factory(Supplier::class)->create();

        $response = $this->putJson('/api/contacts/' . $contact->id, [
            'supplier_id' => $supplier2->id,
            'first_name' => 'Contact',
            'last_name' => 'McTest',
            'phone' => "762.943.3595 x82638",
            'email' => 'contact@mctest.com',
            'role' => 'Contact',
            'notes' => "Labore cupiditate doloribus qui laborum. Voluptatibus deleniti facere quasi sit tenetur accusantium magnam. Ab et consequatur itaque nostrum. Sint voluptas similique nihil."
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'id' => $contact->id,
                    'supplier' => [
                        'id' => $supplier2->id,
                        'name' => $supplier2->name,
                        'description' => $supplier2->description,
                        'address' => $supplier2->address,
                        'phone' => $supplier2->phone,
                        'email' => $supplier2->email,
                        'supplierSince' => $supplier2->supplier_since->toISOString(),
                    ],
                    'firstName' => 'Contact',
                    'lastName' => 'McTest',
                    'phone' => "762.943.3595 x82638",
                    'email' => 'contact@mctest.com',
                    'role' => 'Contact',
                    'notes' => "Labore cupiditate doloribus qui laborum. Voluptatibus deleniti facere quasi sit tenetur accusantium magnam. Ab et consequatur itaque nostrum. Sint voluptas similique nihil."
                ]
            ]);
    }

    public function testUpdate2(): void
    {
        $response = $this->putJson('/api/contacts/0');
        $response->assertStatus(404);
    }

    public function testShow1(): void
    {
        $supplier = factory(Supplier::class)->create();
        $contact = factory(Contact::class)->create(['supplier_id' => $supplier->id]);

        $response = $this->getJson('/api/contacts/' . $contact->id);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'supplier',
                    'firstName',
                    'lastName',
                    'phone',
                    'email',
                    'role',
                    'notes'
                ]
            ]);
    }

    public function testShow2(): void
    {
        $response = $this->getJson('/api/contacts/0');
        $response->assertStatus(404);
    }

    public function testDestroy(): void
    {
        $supplier = factory(Supplier::class)->create();
        $contact = factory(Contact::class)->create(['supplier_id' => $supplier->id]);

        $response = $this->deleteJson('/api/contacts/' . $contact->id);
        $response->assertStatus(204);
        // Check whether the resource is unreachable
        $response = $this->deleteJson('/api/contacts/' . $contact->id);
        $response->assertStatus(404);
        $response = $this->putJson('/api/contacts/' . $contact->id);
        $response->assertStatus(404);
        $response = $this->getJson('/api/contacts/' . $contact->id);
        $response->assertStatus(404);
        // Check the destruction in database
        self::assertNull(Contact::find($contact->id));
    }
}
