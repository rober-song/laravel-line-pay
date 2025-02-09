<?php

namespace Rober\LinePay\Fakes;

use GuzzleHttp\Psr7\Request;
use Illuminate\Support\Testing\Fakes\Fake;
use Rober\LinePay\Contracts\PaymentContract;
use Rober\LinePay\Contracts\ResponseContract;
use Rober\LinePay\Contracts\ResponseCreateContract;
use Rober\LinePay\Enums\LinePayModel;
use Rober\LinePay\Enums\OnlineApi;
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
    protected int|string|null $nonce = null;

    protected const  HTTP_GET = 'GET';
    protected const  HTTP_POST = 'POST';

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

    protected function handleMethod(string $method, string $uri, array $params, array $options): ResponseCreateContract
    {
        $isQuery = $method === 'GET';
        if ($isQuery) {
            $uri .= '?' . http_build_query($params);
        }
        $headers = [
            'X-LINE-Authorization-Nonce' => $this->getNonce(),
            'X-LINE-Authorization'       => $this->getSignature($uri, $params, $isQuery),
        ];
        $request = new Request($method, $uri, $headers, $isQuery ? null : json_encode($params));

        $this->nonce = null;
        $result = array_merge(self::DEFAULT_RETURN, array_shift($this->customizedReturn) ?? []);

        return $this->createResponse::createFromResponse($request, new GuzzleResponse(body: json_encode($result)));
    }

    public static function createPayment(array $config, LinePayModel $model): PaymentContract
    {
        return new self($config);
    }

    public function request($params): ResponseContract
    {
        return $this->handleMethod(
            method:  self::HTTP_POST,
            uri:     OnlineApi::REQUEST->getPath(),
            params:  $params,
            options: OnlineApi::REQUEST->getOptions(),
        );
    }

    public function confirm($transactionId, $params): ResponseContract
    {
        return $this->handleMethod(
            method:  self::HTTP_POST,
            uri:     OnlineApi::CONFIRM->getPath(['transactionId' => $transactionId]),
            params:  $params,
            options: OnlineApi::CONFIRM->getOptions(),
        );
    }

    public function capture($transactionId, $params): ResponseContract
    {
        return $this->handleMethod(
            method:  self::HTTP_POST,
            uri:     OnlineApi::CAPTURE->getPath(['transactionId' => $transactionId]),
            params:  $params,
            options: OnlineApi::CAPTURE->getOptions(),
        );
    }

    public function void($transactionId, $params = []): ResponseContract
    {
        return $this->handleMethod(
            method:  self::HTTP_POST,
            uri:     OnlineApi::VOID->getPath(['transactionId' => $transactionId]),
            params:  $params,
            options: OnlineApi::VOID->getOptions(),
        );
    }

    public function refund($transactionId, $params = []): ResponseContract
    {
        return $this->handleMethod(
            method:  self::HTTP_POST,
            uri:     OnlineApi::REFUND->getPath(['transactionId' => $transactionId]),
            params:  $params,
            options: OnlineApi::REFUND->getOptions(),
        );
    }

    public function paymentDetails($params): ResponseContract
    {
        return $this->handleMethod(
            method:  self::HTTP_GET,
            uri:     OnlineApi::PAYMENT_DETAILS->getPath(),
            params:  $params,
            options: OnlineApi::PAYMENT_DETAILS->getOptions(),
        );
    }

    public function checkPaymentStatus($transactionId): ResponseContract
    {
        return $this->handleMethod(
            method:  self::HTTP_GET,
            uri:     OnlineApi::CHECK_PAYMENT_STATUS->getPath(['transactionId' => $transactionId]),
            params:  [],
            options: OnlineApi::CHECK_PAYMENT_STATUS->getOptions(),
        );
    }

    public function checkPreApprovedRegKey($regKey, $params = []): ResponseContract
    {
        return $this->handleMethod(
            method:  self::HTTP_GET,
            uri:     OnlineApi::CHECK_PRE_APPROVED_REG_KEY->getPath(['regKey' => $regKey]),
            params:  $params,
            options: OnlineApi::CHECK_PRE_APPROVED_REG_KEY->getOptions(),
        );
    }

    public function payPreApproved($regKey, $params): ResponseContract
    {
        return $this->handleMethod(
            method:  self::HTTP_POST,
            uri:     OnlineApi::PAY_PRE_APPROVED->getPath(['regKey' => $regKey]),
            params:  $params,
            options: OnlineApi::PAY_PRE_APPROVED->getOptions(),
        );
    }

    public function expirePreApprovedRegKey($regKey): ResponseContract
    {
        return $this->handleMethod(
            method:  self::HTTP_POST,
            uri:     OnlineApi::EXPIRE_PRE_APPROVED_REG_KEY->getPath(['regKey' => $regKey]),
            params:  [],
            options: OnlineApi::EXPIRE_PRE_APPROVED_REG_KEY->getOptions(),
        );
    }

    public function setNonce($nonce): PaymentContract
    {
        $this->nonce = $nonce;

        return $this;
    }

    private function getNonce()
    {
        return $this->nonce;
    }

    protected function getSignature(string $uri, array $params, bool $isQuery = true): string
    {
        $stringParams = $isQuery ? http_build_query($params) : json_encode($params);

        $signature = sprintf('%s%s%s%s', $this->config['channel_secret'], $uri, $stringParams, $this->getNonce());

        return base64_encode(hash_hmac('sha256', $signature, $this->config['channel_secret'], true));
    }
}
