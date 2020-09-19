<?php

namespace Tests\Feature;

use App\Models\Contact;
use App\Models\Supplier;
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
        Contact::factory()->count(2)->create();

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
        $supplier = Supplier::factory()->create();

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
        $contact = Contact::factory()->create();
        $newSupplier = Supplier::factory()->create();

        $response = $this->putJson('/api/contacts/' . $contact->id, [
            'supplier_id' => $newSupplier->id,
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
                        'id' => $newSupplier->id,
                        'name' => $newSupplier->name,
                        'notes' => $newSupplier->notes,
                        'address' => $newSupplier->address,
                        'phone' => $newSupplier->phone,
                        'email' => $newSupplier->email,
                        'supplierSince' => $newSupplier->supplier_since->toISOString(),
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
        $contact = Contact::factory()->create();

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
        $contact = Contact::factory()->create();

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
