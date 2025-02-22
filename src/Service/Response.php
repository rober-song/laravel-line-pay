<?php

namespace Rober\LinePay\Service;

use GuzzleHttp\Psr7\Request;
use Rober\LinePay\Contracts\ResponseContract;
use Rober\LinePay\Contracts\ResponseCreateContract;
use GuzzleHttp\Psr7\Response as GuzzleResponse;
use Rober\LinePay\Exceptions\ResponseException;

class Response implements ResponseContract, ResponseCreateContract
{
    /** @var array{returnCode: string, returnMessage: string, info: array} */
    protected array $data = [];

    /**
     * @throws ResponseException
     */
    public function __construct(
        protected Request $request,
        protected GuzzleResponse $response,
    ) {
        $body = $response->getBody();
        $this->data = json_decode($body, true) ?? [];
        if (empty($this->data)) {
            throw new ResponseException('Response data is not json' . $body);
        }
    }

    public static function createFromResponse(Request $request, GuzzleResponse $response): self
    {
        return app(static::class, ['request' => $request, 'response' => $response]);
    }

    public function getReturnCode(): string
    {
        return $this->data['returnCode'];
    }

    public function getReturnMessage(): string
    {
        return $this->data['returnMessage'];
    }

    public function getInfo()
    {
        return $this->data['info'];
    }

    public function isSuccess(): bool
    {
        return $this->getReturnCode() === self::SUCCESS_CODE;
    }

    public function getRequest(): Request
    {
        return $this->request;
    }

    public function getResponse(): GuzzleResponse
    {
        return $this->response;
    }
}
