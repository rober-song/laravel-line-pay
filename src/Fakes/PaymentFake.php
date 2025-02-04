<?php

namespace Rober\LinePay\Fakes;

use Illuminate\Support\Testing\Fakes\Fake;
use Rober\LinePay\Contracts\PaymentContract;
use Rober\LinePay\Contracts\ResponseContract;
use Rober\LinePay\Contracts\ResponseCreateContract;
use Rober\LinePay\Enums\LinePayModel;
use Rober\LinePay\Service\Response;

/**
 * Payment Service Fake
 */
class PaymentFake implements PaymentContract, Fake
{
    /** @var ResponseCreateContract|string */
    protected ResponseCreateContract|string $createResponse;

    public function __construct(protected array $config)
    {
        $this->createResponse = $this->config['create_response'] ?? Response::class;
    }


    public static function createPayment(array $config, LinePayModel $model): PaymentContract
    {
        return new self($config);
    }

    public function request($params): ResponseContract
    {
        return $this->createResponse::createFromResponse(new \GuzzleHttp\Psr7\Response(body: json_encode(['returnCode' => '0000', 'returnMessage' => 'Success', 'info' => []])));
    }

    public function confirm($transactionId, $params): ResponseContract
    {
        // TODO: Implement confirm() method.
    }

    public function capture($transactionId, $params): ResponseContract
    {
        // TODO: Implement capture() method.
    }

    public function void($transactionId, $params = []): ResponseContract
    {
        // TODO: Implement void() method.
    }

    public function refund($transactionId, $params = []): ResponseContract
    {
        // TODO: Implement refund() method.
    }

    public function paymentDetails($params): ResponseContract
    {
        // TODO: Implement paymentDetails() method.
    }

    public function checkPaymentStatus($transactionId): ResponseContract
    {
        // TODO: Implement checkPaymentStatus() method.
    }

    public function checkPreApprovedRegKey($regKey, $params = []): ResponseContract
    {
        // TODO: Implement checkPreApprovedRegKey() method.
    }

    public function payPreApproved($regKey, $params): ResponseContract
    {
        // TODO: Implement payPreApproved() method.
    }

    public function expirePreApprovedRegKey($regKey): ResponseContract
    {
        // TODO: Implement expirePreApprovedRegKey() method.
    }

    public function setNonce($nonce): PaymentContract
    {
        // TODO: Implement setNonce() method.
    }
}
