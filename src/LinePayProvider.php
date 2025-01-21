<?php

namespace Rober\LinePay;

use Illuminate\Support\ServiceProvider;
use Rober\LinePay\Enums\LinePayModel;
use Rober\LinePay\Service\Payment;

class LinePayProvider extends ServiceProvider
{
    public function register()
    {
        $configPath = __DIR__ . '/../config/line_pay.php';
        $this->publishes([$configPath => config_path('line_pay.php')], 'config');
        $this->mergeConfigFrom($configPath, 'line_pay');
    }

    public function boot()
    {
        $this->app->bind('line-pay', function ($app) {
            $options = $app['config']->get('line_pay.options');
            $model = LinePayModel::tryFrom($app['config']->get('line_pay.provider')) ?? LinePayModel::SANDBOX;

            return new Payment($options, $model);
        });
    }
}
