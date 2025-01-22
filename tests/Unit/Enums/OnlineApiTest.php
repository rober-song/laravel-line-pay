<?php

use Rober\LinePay\Enums\OnlineApi;
use Rober\LinePay\Exceptions\RouteParamException;

covers(OnlineApi::class);

describe('[例外]', function () {
    test('未取得必要路由參數', function () {
        $exceptions = collect([
                    OnlineApi::CONFIRM,
                    OnlineApi::CAPTURE,
                    OnlineApi::VOID,
                    OnlineApi::REFUND,
                    OnlineApi::CHECK_PAYMENT_STATUS,
                    OnlineApi::CHECK_PRE_APPROVED_REG_KEY,
                    OnlineApi::PAY_PRE_APPROVED,
                    OnlineApi::EXPIRE_PRE_APPROVED_REG_KEY,
                ])->map(fn(OnlineApi $api) => fn() => $api->getPath());
        expect($exceptions)->each->toThrow(RouteParamException::class);
    });
});

describe('[OnlineApi]', function () {
    test('REQUEST', function () {
        $api = OnlineApi::REQUEST;
        expect($api->getPath())->toBe('/v3/payments/request')
            ->and($api->getOptions())
            ->toBe([
                       'connect_timeout' => 5,
                       'timeout'         => 20,
                   ]);
    });
    test('CONFIRM', function () {
        $api = OnlineApi::CONFIRM;
        expect($api->getPath(['transactionId' => '1234567890']))->toBe('/v3/payments/1234567890/confirm')
            ->and($api->getOptions())
            ->toBe([
                       'connect_timeout' => 5,
                       'timeout'         => 40,
                   ]);
    });
    test('CAPTURE', function () {
        $api = OnlineApi::CAPTURE;
        expect($api->getPath(['transactionId' => '1234567890']))->toBe('/v3/payments/authorizations/1234567890/capture')
            ->and($api->getOptions())
            ->toBe([
                       'connect_timeout' => 5,
                       'timeout'         => 60,
                   ]);
    });
    test('VOID', function () {
        $api = OnlineApi::VOID;
        expect($api->getPath(['transactionId' => '1234567890']))->toBe('/v3/payments/authorizations/1234567890/void')
            ->and($api->getOptions())
            ->toBe([
                       'connect_timeout' => 5,
                       'timeout'         => 20,
                   ]);
    });
    test('REFUND', function () {
        $api = OnlineApi::REFUND;
        expect($api->getPath(['transactionId' => '1234567890']))->toBe('/v3/payments/1234567890/refund')
            ->and($api->getOptions())
            ->toBe([
                       'connect_timeout' => 5,
                       'timeout'         => 20,
                   ]);
    });
    test('PAYMENT_DETAILS', function () {
        $api = OnlineApi::PAYMENT_DETAILS;
        expect($api->getPath())->toBe('/v3/payments')
            ->and($api->getOptions())
            ->toBe([
                       'connect_timeout' => 5,
                       'timeout'         => 20,
                   ]);
    });
    test('CHECK_PAYMENT_STATUS', function () {
        $api = OnlineApi::CHECK_PAYMENT_STATUS;
        expect($api->getPath(['transactionId' => '1234567890']))->toBe('/v3/payments/requests/1234567890/check')
            ->and($api->getOptions())
            ->toBe([
                       'connect_timeout' => 5,
                       'timeout'         => 20,
                   ]);
    });
});
