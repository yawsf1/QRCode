<?php
namespace Tests\Feature;
use App\Models\Contact;
use Tests\TestCase;
class ContactTest extends TestCase
{
    public function test_contact_form_can_be_submitted(): void
    {
        $payload = [
            'nom' => 'Laayadi',
            'prenom' => 'Youssef',
            'email' => 'contact@test.com',
            'message' => 'Bonjour, je souhaite une démo.',
        ];
        $this->post(route('contact.send'), $payload)
            ->assertRedirect()
            ->assertSessionHas('success');
        $this->assertDatabaseHas('contacts', ['email' => 'contact@test.com']);
    }
    public function test_contact_form_rejects_duplicate_email(): void
    {
        Contact::create([
            'nom' => 'A',
            'prenom' => 'B',
            'email' => 'duplicate@test.com',
            'message' => 'First message',
        ]);
        $this->post(route('contact.send'), [
            'nom' => 'C',
            'prenom' => 'D',
            'email' => 'duplicate@test.com',
            'message' => 'Second message',
        ])->assertSessionHasErrors('email');
    }
}
