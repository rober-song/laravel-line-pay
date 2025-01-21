<?php

namespace Rober\LinePay\Tests;

use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Facade;
use Rober\LinePay\Facades\LinePay;
use Rober\LinePay\LinePayProvider;

abstract class TestCase extends \Orchestra\Testbench\TestCase
{
    protected function getPackageProviders($app): array
    {
        return [LinePayProvider::class];
    }

    /**
     * Override application aliases.
     *
     * @param  Application  $app
     * @return array<string, class-string<Facade>>
     */
    protected function getPackageAliases($app)
    {
        return [
            'line-pay' => LinePay::class,
        ];
    }
}
