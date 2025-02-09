<?php

use Rober\LinePay\Contracts\PaymentContract;
use Rober\LinePay\Contracts\ResponseContract;
use Rober\LinePay\Facades\LinePay;

covers(PaymentContract::class);

test('模擬 fake facade', function () {
    LinePay::fake();
    collect([
        fn() => LinePay::request([]),
        fn() => LinePay::confirm(100, []),
        fn() => LinePay::capture(100, []),
        fn() => LinePay::void(100),
        fn() => LinePay::refund(100),
        fn() => LinePay::paymentDetails([]),
        fn() => LinePay::checkPaymentStatus(100),
        fn() => LinePay::checkPreApprovedRegKey('key'),
        fn() => LinePay::payPreApproved('key', []),
        fn() => LinePay::expirePreApprovedRegKey('key'),
    ])->each(fn ($fn) => expect($fn())->toBeInstanceOf(ResponseContract::class));
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
