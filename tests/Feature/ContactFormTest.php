<?php

declare(strict_types=1);

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ContactFormTest extends TestCase
{
    use RefreshDatabase;

    private array $validPayload;

    protected function setUp(): void
    {
        parent::setUp();

        $this->validPayload = [
            'name'            => 'Jane Smith',
            'company'         => 'Acme Corp',
            'email'           => 'jane@acmecorp.com',
            'service'         => 'software-development',
            'project_details' => 'We need help building a custom internal HR tool that integrates with existing systems.',
        ];
    }

    // ── Happy path ─────────────────────────────────────────────────────────

    public function test_valid_ajax_submission_returns_success(): void
    {
        $this->postJson(route('contact.submit'), $this->validPayload)
             ->assertStatus(200)
             ->assertJson(['success' => true]);
    }

    public function test_valid_submission_saves_to_database(): void
    {
        $this->postJson(route('contact.submit'), $this->validPayload);
        $this->assertDatabaseHas('inquiries', [
            'email'   => 'jane@acmecorp.com',
            'service' => 'software-development',
        ]);
    }

    public function test_database_record_has_all_fields(): void
    {
        $this->postJson(route('contact.submit'), $this->validPayload);
        $this->assertDatabaseHas('inquiries', [
            'name'    => 'Jane Smith',
            'company' => 'Acme Corp',
            'email'   => 'jane@acmecorp.com',
            'service' => 'software-development',
        ]);
    }

    public function test_non_js_valid_submission_redirects_with_success(): void
    {
        $this->post(route('contact.submit'), $this->validPayload)
             ->assertRedirect(route('home'))
             ->assertSessionHas('inquiry_success', true);
    }

    public function test_non_js_valid_submission_saves_to_database(): void
    {
        $this->post(route('contact.submit'), $this->validPayload);
        $this->assertDatabaseHas('inquiries', ['email' => 'jane@acmecorp.com']);
    }

    // ── Validation failures ────────────────────────────────────────────────

    public function test_name_required(): void
    {
        $this->postJson(route('contact.submit'), array_merge($this->validPayload, ['name' => '']))
             ->assertStatus(422)->assertJsonValidationErrors(['name']);
        $this->assertDatabaseMissing('inquiries', ['email' => 'jane@acmecorp.com']);
    }

    public function test_company_required(): void
    {
        $this->postJson(route('contact.submit'), array_merge($this->validPayload, ['company' => '']))
             ->assertStatus(422)->assertJsonValidationErrors(['company']);
    }

    public function test_email_required(): void
    {
        $this->postJson(route('contact.submit'), array_merge($this->validPayload, ['email' => '']))
             ->assertStatus(422)->assertJsonValidationErrors(['email']);
    }

    public function test_email_must_be_valid(): void
    {
        $this->postJson(route('contact.submit'), array_merge($this->validPayload, ['email' => 'not-an-email']))
             ->assertStatus(422)->assertJsonValidationErrors(['email']);
    }

    public function test_service_required(): void
    {
        $this->postJson(route('contact.submit'), array_merge($this->validPayload, ['service' => '']))
             ->assertStatus(422)->assertJsonValidationErrors(['service']);
    }

    public function test_service_must_be_valid_option(): void
    {
        $this->postJson(route('contact.submit'), array_merge($this->validPayload, ['service' => 'invalid-service']))
             ->assertStatus(422)->assertJsonValidationErrors(['service']);
    }

    public function test_project_details_required(): void
    {
        $this->postJson(route('contact.submit'), array_merge($this->validPayload, ['project_details' => '']))
             ->assertStatus(422)->assertJsonValidationErrors(['project_details']);
    }

    public function test_project_details_too_short(): void
    {
        $this->postJson(route('contact.submit'), array_merge($this->validPayload, ['project_details' => 'Too short.']))
             ->assertStatus(422)->assertJsonValidationErrors(['project_details']);
    }

    public function test_project_details_too_long(): void
    {
        $this->postJson(route('contact.submit'), array_merge($this->validPayload, ['project_details' => str_repeat('a', 2001)]))
             ->assertStatus(422)->assertJsonValidationErrors(['project_details']);
    }

    public function test_non_js_invalid_submission_returns_errors(): void
    {
        $this->post(route('contact.submit'), array_merge($this->validPayload, ['name' => '']))
             ->assertSessionHasErrors(['name']);
    }

    // ── All 6 service options are valid ────────────────────────────────────

    public function test_all_service_options_accepted(): void
    {
        $services = [
            'software-development', 'data-analytics', 'software-qa',
            'business-analysis', 'healthcare-technology', 'not-sure',
        ];
        foreach ($services as $service) {
            $this->postJson(route('contact.submit'), array_merge($this->validPayload, ['service' => $service]))
                 ->assertStatus(200)
                 ->assertJson(['success' => true]);
        }
    }
}