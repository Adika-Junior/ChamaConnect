<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Foundation\Testing\CreatesApplication;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    protected function setUp(): void
    {
        parent::setUp();

    // Disable all middleware for tests to avoid 419/redirects and keep
    // focused on application logic. If you prefer only disabling CSRF,
    // switch to withoutMiddleware(VerifyCsrfToken::class).
    $this->withoutMiddleware();
    }
}
