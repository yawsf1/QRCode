<?php
namespace Tests;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Tests\Concerns\CreatesUsers;
abstract class TestCase extends BaseTestCase
{
    use CreatesUsers;
    use RefreshDatabase;
}
