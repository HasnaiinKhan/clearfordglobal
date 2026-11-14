<?php

declare(strict_types=1);

namespace Tests\Feature;

use Tests\TestCase;

class ExampleTest extends TestCase
{
    public function test_homepage_returns_200(): void
    {
        $this->get('/')->assertStatus(200);
    }
}
