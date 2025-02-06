<?php

use Rober\LinePay\Contracts\PaymentContract;
use Rober\LinePay\Facades\LinePay;

covers(PaymentContract::class);

test('模擬 fake facade', function () {
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

test('[info] 模擬回傳結果', function () {
    $response = [
        'orderId'       => 'MKSI_M_20180904_1000001',
        'transactionId' => 2018082512345678910,
        'payInfo'       => [
            ['method' => 'BALANCE', 'amount' => 900],
            ['method' => 'DISCOUNT', 'amount' => 100],
        ],
    ];
    LinePay::fake()->setReturn(info: $response);
    expect(LinePay::request([])->getInfo())->toBe($response);
});

test('[code] 模擬回傳code', function () {
    LinePay::fake()->setReturn(code: '1101', message: 'Not a LINE Pay member');

    $response = LinePay::confirm(100, []);
    expect($response->isSuccess())
        ->toBeFalse()
        ->and($response->getReturnCode())
        ->toBe('1101')
        ->and($response->getReturnMessage())
        ->toBe('Not a LINE Pay member');
});

test('[info] 模擬多個執行', function () {
    LinePay::fake()
        ->setReturn(info: ['orderId' => 'A',])
        ->setReturn(info: ['orderId' => 'B',]);

    expect(LinePay::confirm(100, [])->getInfo())
        ->toBe(['orderId' => 'A'])
        ->and(LinePay::confirm(100, [])->getInfo())
        ->toBe(['orderId' => 'B']);
});
