<?php

namespace Rober\LinePay\Contracts;

interface ResponseContract
{
    public const SUCCESS_CODE = '0000';

    public function getReturnCode(): string;

    public function getReturnMessage(): string;

    /** @return array | mixed */
    public function getInfo();

    public function isSuccess(): bool;
}
