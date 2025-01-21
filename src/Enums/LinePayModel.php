<?php

namespace Rober\LinePay\Enums;

enum LinePayModel: string
{
    case SANDBOX = 'sandbox';
    case PRODUCTION = 'production';

    public function getHost(): string
    {
        return match ($this) {
          self::SANDBOX => 'https://sandbox-api-pay.line.me',
          self::PRODUCTION => 'https://api-pay.line.me',
        };
    }

    public function isDebug(): bool
    {
        return match ($this) {
          self::SANDBOX => true,
          self::PRODUCTION => false,
        };
    }
}
