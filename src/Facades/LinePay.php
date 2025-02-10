<?php

namespace Rober\LinePay\Facades;

use Illuminate\Support\Facades\Facade;
use Rober\LinePay\Contracts\PaymentContract;
use Rober\LinePay\Contracts\ResponseContract;
use Rober\LinePay\Enums\LinePayModel;
use Rober\LinePay\Fakes\PaymentFake;

/**
 * @method static ResponseContract request(array $params)
 * @method static ResponseContract confirm($transactionId, array $params)
 * @method static ResponseContract capture($transactionId, array $params)
 * @method static ResponseContract void($transactionId, array $params = [])
 * @method static ResponseContract refund($transactionId, array $params = [])
 * @method static ResponseContract paymentDetails(array $params)
 * @method static ResponseContract checkPaymentStatus($transactionId)
 * @method static ResponseContract checkPreApprovedRegKey($regKey, array $params = [])
 * @method static ResponseContract payPreApproved($regKey, array $params)
 * @method static ResponseContract expirePreApprovedRegKey($regKey)
 * @method static PaymentContract setNonce(int|string|null $nonce)
 *
 * @see \Rober\LinePay\Service\Payment
 */
class LinePay extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'line-pay';
    }

    /**
     * Replace the bound instance with a fake.
     *
     * @return PaymentFake
     */
    public static function fake()
    {
        return tap(new PaymentFake(static::$app['config']->get('line_pay.options', [])), function ($fake) {
            static::swap($fake);
        });
    }
}
