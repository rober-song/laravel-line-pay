<?php

namespace Rober\LinePay\Contracts;

use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;

interface ResponseContract
{
    public const SUCCESS_CODE = '0000';

    public function getReturnCode(): string;

    public function getReturnMessage(): string;

    public function getRequest(): Request;

    public function getResponse(): Response;

    /** @return array | mixed */
    public function getInfo();

    public function isSuccess(): bool;
}
