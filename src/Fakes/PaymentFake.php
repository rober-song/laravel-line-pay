<?php

namespace Rober\LinePay\Fakes;

use GuzzleHttp\Psr7\Response as GuzzleResponse;
use Illuminate\Support\Testing\Fakes\Fake;
use Rober\LinePay\Contracts\PaymentContract;
use Rober\LinePay\Contracts\ResponseContract;
use Rober\LinePay\Contracts\ResponseCreateContract;
use Rober\LinePay\Enums\LinePayModel;
use Rober\LinePay\Enums\OnlineApi;
use Rober\LinePay\Service\Payment;

/**
 * Payment Service Fake
 */
class PaymentFake extends Payment implements PaymentContract, Fake
{
    protected const DEFAULT_RETURN = ['returnCode' => '0000', 'returnMessage' => 'Success', 'info' => []];

    /** @var ResponseCreateContract|string */
    protected ResponseCreateContract|string $createResponse;

    /** @var array{returnCode: string, returnMessage: string, info: array}[] */
    protected array $customizedReturn = [];

    public function __construct(protected array $config)
    {
        parent::__construct($config, LinePayModel::SANDBOX);
    }

    public function setReturn(?string $code = null, ?string $message = null, array $info = []): self
    {
        $this->customizedReturn[] = [
            'returnCode'    => $code ?? self::DEFAULT_RETURN['returnCode'],
            'returnMessage' => $message ?? self::DEFAULT_RETURN['returnMessage'],
            'info'          => $info
        ];

        return $this;
    }


    protected function handleMethod(OnlineApi $api, string $uri, array $params): ResponseContract
    {
        $request = $this->createRequest($api->getMethod(), $uri, $params);
        $result = array_merge(self::DEFAULT_RETURN, array_shift($this->customizedReturn) ?? []);

        return $this->createResponse::createFromResponse($request, new GuzzleResponse(body: json_encode($result)));
    }
}
