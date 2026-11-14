<?php

declare(strict_types=1);

namespace Tests\Feature;

use Tests\TestCase;

class HomepageTest extends TestCase
{
    public function test_homepage_returns_200(): void
    {
        $this->get('/')->assertStatus(200);
    }

    public function test_homepage_contains_h1(): void
    {
        $response = $this->get('/');
        $response->assertSee('Build better systems. Run better operations.');
    }

    public function test_homepage_contains_home_section(): void
    {
        $this->get('/')->assertSee('id="home"', false);
    }

    public function test_homepage_contains_services_section(): void
    {
        $this->get('/')->assertSee('id="services"', false);
    }

    public function test_homepage_contains_contact_section(): void
    {
        $this->get('/')->assertSee('id="contact"', false);
    }

    public function test_homepage_contains_csrf_token(): void
    {
        $this->get('/')->assertSee('csrf-token', false);
    }

    public function test_homepage_contains_all_five_services(): void
    {
        $response = $this->get('/');
        $response->assertSee('Software Development');
        $response->assertSee('Data Analytics');
        $response->assertSee('Software QA');
        $response->assertSee('Business Analysis');
        $response->assertSee('Healthcare Technology');
    }

    public function test_homepage_meta_description_present(): void
    {
        $this->get('/')->assertSee('name="description"', false);
    }
}