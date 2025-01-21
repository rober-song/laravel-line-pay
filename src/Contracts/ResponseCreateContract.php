<?php

namespace Rober\LinePay\Contracts;

use GuzzleHttp\Psr7\Response;

interface ResponseCreateContract
{
    public static function createFromResponse(Response $response): ResponseCreateContract;
}
