<?php

namespace Rober\LinePay\Contracts;

use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;

interface ResponseCreateContract
{
    public static function createFromResponse(Request $request, Response $response): ResponseContract;
}
