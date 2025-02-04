<?php

use Rober\LinePay\Contracts\PaymentContract;
use Rober\LinePay\Facades\LinePay;

covers(PaymentContract::class);

test('test fake facade', function () {
    LinePay::fake();
    collect([
        'request',
        'confirm',
        'capture',
        'void',
        'refund',
        'paymentDetails',
        'checkPaymentStatus',
        'checkPreApprovedRegKey',
        'payPreApproved',
        'expirePreApprovedRegKey',
    ])->each(function ($method) {
        $response = LinePay::$method(...\Illuminate\Support\Collection::times(3));
        expect($response->isSuccess())->toBeTrue();
    });
});
