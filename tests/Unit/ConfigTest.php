<?php

namespace Rober\LinePay\Tests\Unit;

use PHPUnit\Framework\Attributes\CoversNothing;
use Rober\LinePay\Facades\LinePay;
use Rober\LinePay\Service\Payment;
use Rober\LinePay\Tests\TestCase;

#[CoversNothing]
class ConfigTest extends TestCase
{
    public function testFacade()
    {
        $alias = LinePay::getFacadeRoot();
        $this->assertInstanceOf(Payment::class, $alias);
    }

    public function testDefaultConfig()
    {
        $config = $this->app['config']->get('line_pay');
        $this->assertArrayHasKey('provider', $config);
        $this->assertArrayHasKey('options', $config);
    }
}
