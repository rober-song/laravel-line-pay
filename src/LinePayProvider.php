<?php

namespace Rober\LinePay;

use Illuminate\Support\ServiceProvider;

class LinePayProvider extends ServiceProvider
{
    public function boot()
    {
        $configPath = __DIR__ . '/../config/line_pay.php';
        $this->publishes([$configPath => config_path('line_pay.php')], 'config');
        $this->mergeConfigFrom($configPath, 'line_pay');
    }
}
