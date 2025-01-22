<?php

use Rober\LinePay\Enums\OnlineApi;
use Rober\LinePay\Exceptions\RouteParamException;

covers(\Rober\LinePay\Enums\LinePayModel::class);

test('取得對應的路徑', function () {
    $model = \Rober\LinePay\Enums\LinePayModel::SANDBOX;
    expect($model->getHost())->toBe('https://sandbox-api-pay.line.me');
    $model = \Rober\LinePay\Enums\LinePayModel::PRODUCTION;
    expect($model->getHost())->toBe('https://api-pay.line.me');
});

test('是否需要debug', function () {
    $model = \Rober\LinePay\Enums\LinePayModel::SANDBOX;
    expect($model->isDebug())->toBeTrue();
    $model = \Rober\LinePay\Enums\LinePayModel::PRODUCTION;
    expect($model->isDebug())->toBeFalse();
});
