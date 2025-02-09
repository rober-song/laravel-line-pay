<?php

namespace Rober\LinePay\Service;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\RequestOptions;
use Ramsey\Uuid\Uuid;
use Rober\LinePay\Contracts\PaymentContract;
use Rober\LinePay\Contracts\ResponseContract;
use Rober\LinePay\Contracts\ResponseCreateContract;
use Rober\LinePay\Enums\LinePayModel;
use Rober\LinePay\Enums\OnlineApi;

/**
 * LINE Pay Online Payment Service
 */
class Payment implements PaymentContract
{
    protected Client $client;

    protected const  HTTP_GET = 'GET';
    protected const  HTTP_POST = 'POST';

    protected int|string|null $nonce = null;

    /** @var ResponseCreateContract|string */
    protected ResponseCreateContract|string $createResponse;

    /**
     * @param array{channel_id: string, channel_secret: string, merchant_device_profile_id: string, create_response: string} $config
     * @param LinePayModel $model
     */
    public function __construct(
        protected array $config,
        protected LinePayModel $model,
    ) {
        $headers = array_merge(
            [
                'Content-Type'     => 'application/json',
                'X-LINE-ChannelId' => $this->config['channel_id'],
            ],
            $this->config['merchant_device_profile_id']
                ? ['X-LINE-MerchantDeviceProfileId' => $this->config['merchant_device_profile_id']]
                : [],
        );
        $this->client = new Client(
            [
                'base_uri'    => $this->model->getHost(),
                'headers'     => $headers,
                'http_errors' => false,
                'debug'       => $this->model->isDebug(),
            ]
        );

        $this->createResponse = $this->config['create_response'] ?? Response::class;
    }

    public function request($params): ResponseContract
    {
        $api = OnlineApi::REQUEST;

        return $this->handleMethod(
            method:  self::HTTP_POST,
            uri:     $api->getPath(),
            params:  $params,
            options: $api->getOptions(),
        );
    }

    public function confirm($transactionId, $params): ResponseContract
    {
        $api = OnlineApi::CONFIRM;

        return $this->handleMethod(
            method:  self::HTTP_POST,
            uri:     $api->getPath(['transactionId' => $transactionId]),
            params:  $params,
            options: $api->getOptions(),
        );
    }

    public function capture($transactionId, $params): ResponseContract
    {
        $api = OnlineApi::CAPTURE;

        return $this->handleMethod(
            method:  self::HTTP_POST,
            uri:     $api->getPath(['transactionId' => $transactionId]),
            params:  $params,
            options: $api->getOptions(),
        );
    }

    public function void($transactionId, $params = []): ResponseContract
    {
        $api = OnlineApi::VOID;

        return $this->handleMethod(
            method:  self::HTTP_POST,
            uri:     $api->getPath(['transactionId' => $transactionId]),
            params:  $params,
            options: $api->getOptions(),
        );
    }

    public function refund($transactionId, $params = []): ResponseContract
    {
        $api = OnlineApi::REFUND;

        return $this->handleMethod(
            method:  self::HTTP_POST,
            uri:     $api->getPath(['transactionId' => $transactionId]),
            params:  $params,
            options: $api->getOptions(),
        );
    }

    public function paymentDetails($params): ResponseContract
    {
        $api = OnlineApi::PAYMENT_DETAILS;

        return $this->handleMethod(
            method:  self::HTTP_POST,
            uri:     $api->getPath(),
            params:  $params,
            options: $api->getOptions(),
        );
    }

    public function checkPaymentStatus($transactionId): ResponseContract
    {
        $api = OnlineApi::PAYMENT_DETAILS;

        return $this->handleMethod(
            method:  self::HTTP_GET,
            uri:     $api->getPath(['transactionId' => $transactionId]),
            params:  [],
            options: $api->getOptions(),
        );
    }

    public function checkPreApprovedRegKey($regKey, $params = []): ResponseContract
    {
        $api = OnlineApi::CHECK_PRE_APPROVED_REG_KEY;

        return $this->handleMethod(
            method:  self::HTTP_GET,
            uri:     $api->getPath(['regKey' => $regKey]),
            params:  $params,
            options: $api->getOptions(),
        );
    }

    public function payPreApproved($regKey, $params): ResponseContract
    {
        $api = OnlineApi::PAY_PRE_APPROVED;

        return $this->handleMethod(
            method:  self::HTTP_POST,
            uri:     $api->getPath(['regKey' => $regKey]),
            params:  $params,
            options: $api->getOptions(),
        );
    }

    public function expirePreApprovedRegKey($regKey): ResponseContract
    {
        $api = OnlineApi::EXPIRE_PRE_APPROVED_REG_KEY;

        return $this->handleMethod(
            method:  self::HTTP_POST,
            uri:     $api->getPath(['regKey' => $regKey]),
            params:  [],
            options: $api->getOptions(),
        );
    }

    public function setNonce($nonce): PaymentContract
    {
        $this->nonce = $nonce;

        return $this;
    }

    protected function getSignature(string $uri, array $params, bool $isQuery = true): string
    {
        $stringParams = $isQuery ? http_build_query($params) : json_encode($params);

        $signature = sprintf('%s%s%s%s', $this->config['channel_secret'], $uri, $stringParams, $this->getNonce());

        return base64_encode(hash_hmac('sha256', $signature, $this->config['channel_secret'], true));
    }

    protected function getNonce(): int|string
    {
        $this->nonce ??= Uuid::uuid4()->toString();

        return $this->nonce;
    }

    protected function handleMethod(string $method, string $uri, array $params, array $options): Response
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
        $response = $this->client->send($request, $options);
        $this->nonce = null;

        return $this->createResponse::createFromResponse($request, $response);
    }

    public static function createPayment(array $config, LinePayModel $model): PaymentContract
    {
        return new static($config, $model);
    }
}
