<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Database\Seeders\UnitTestDatabaseSeeder;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;
    protected $seed = true;
    protected $seeder = UnitTestDatabaseSeeder::class;
}
