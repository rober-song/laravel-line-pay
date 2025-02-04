<?php

use Rober\LinePay\Contracts\PaymentContract;
use Rober\LinePay\Facades\LinePay;

covers(PaymentContract::class);

test('test fake facade', function () {
    LinePay::fake();
    $response = LinePay::request([]);
    expect($response->isSuccess())->toBeTrue();
});
