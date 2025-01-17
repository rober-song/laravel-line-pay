<?php

namespace Rober\LinePay\Tests\Unit;

use PHPUnit\Framework\Attributes\CoversNothing;
use Rober\LinePay\Tests\TestCase;

#[CoversNothing]
class ConfigTest extends TestCase
{
    public function testPublishesConfig()
    {
        $this->artisan('vendor:publish', ['--tag' => 'config'])
            ->assertExitCode(0);

        $configPath = config_path('line_pay.php');

        $this->assertFileExists($configPath);
    }
}
