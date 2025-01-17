<?php

namespace Rober\LinePay\Tests;

use Rober\LinePay\LinePayProvider;

abstract class TestCase extends \Orchestra\Testbench\TestCase
{
    protected function getPackageProviders($app): array
    {
        return [LinePayProvider::class];
    }
}
