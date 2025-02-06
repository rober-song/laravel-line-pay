<?php

namespace Rober\LinePay\Fakes;

use Illuminate\Support\Testing\Fakes\Fake;
use Rober\LinePay\Contracts\PaymentContract;
use Rober\LinePay\Contracts\ResponseContract;
use Rober\LinePay\Contracts\ResponseCreateContract;
use Rober\LinePay\Enums\LinePayModel;
use Rober\LinePay\Service\Response;
use GuzzleHttp\Psr7\Response as GuzzleResponse;

/**
 * Payment Service Fake
 */
class PaymentFake implements PaymentContract, Fake
{
    protected const DEFAULT_RETURN = ['returnCode' => '0000', 'returnMessage' => 'Success', 'info' => []];

    /** @var ResponseCreateContract|string */
    protected ResponseCreateContract|string $createResponse;

    /** @var array{returnCode: string, returnMessage: string, info: array}[] */
    protected array $customizedReturn = [];

    public function __construct(protected array $config)
    {
        $this->createResponse = $this->config['create_response'] ?? Response::class;
    }

    public function setReturn(string $code = null, string $message = null, array $info = []): self
    {
        $this->customizedReturn[] = [
            'returnCode'    => $code ?? self::DEFAULT_RETURN['returnCode'],
            'returnMessage' => $message ?? self::DEFAULT_RETURN['returnMessage'],
            'info'          => $info
        ];

        return $this;
    }

    protected function handleMethod(): ResponseCreateContract
    {
        $result = array_merge(self::DEFAULT_RETURN, array_shift($this->customizedReturn) ?? []);

        return $this->createResponse::createFromResponse(new GuzzleResponse(body: json_encode($result)));
    }

    public static function createPayment(array $config, LinePayModel $model): PaymentContract
    {
        return new self($config);
    }

    public function request($params): ResponseContract
    {
        return $this->handleMethod();
    }

    public function confirm($transactionId, $params): ResponseContract
    {
        return $this->handleMethod();
    }

    public function capture($transactionId, $params): ResponseContract
    {
        return $this->handleMethod();
    }

    public function void($transactionId, $params = []): ResponseContract
    {
        return $this->handleMethod();
    }

    public function refund($transactionId, $params = []): ResponseContract
    {
        return $this->handleMethod();
    }

    public function paymentDetails($params): ResponseContract
    {
        return $this->handleMethod();
    }

    public function checkPaymentStatus($transactionId): ResponseContract
    {
        return $this->handleMethod();
    }

    public function checkPreApprovedRegKey($regKey, $params = []): ResponseContract
    {
        return $this->handleMethod();
    }

    public function payPreApproved($regKey, $params): ResponseContract
    {
        return $this->handleMethod();
    }

    public function expirePreApprovedRegKey($regKey): ResponseContract
    {
        return $this->handleMethod();
    }

    public function setNonce($nonce): PaymentContract
    {
        return $this;
    }
}
