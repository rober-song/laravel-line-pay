<?php

namespace Rober\LinePay\Enums;

use Illuminate\Routing\Route;
use Rober\LinePay\Exceptions\RouteParamException;

enum OnlineApi
{
    case REQUEST;
    case CONFIRM;
    case CAPTURE;
    case VOID;
    case REFUND;
    case PAYMENT_DETAILS;
    case CHECK_PAYMENT_STATUS;
    case CHECK_PRE_APPROVED_REG_KEY;
    case PAY_PRE_APPROVED;
    case EXPIRE_PRE_APPROVED_REG_KEY;

    /**
     * @param array $routeParams
     * @return string
     * @throws RouteParamException
     */
    public function getPath(array $routeParams = []): string
    {
        $path = $this->getOriginPath();
        [$replaces, $keys] = $this->compileParameterNames();

        $params = array_map(
            fn($key) => $routeParams[$key] ?? throw RouteParamException::forMissingParameters(
                new Route('', $path, ['as' => $this->name]),
                [$key]
            ),
            $keys
        );
        $transform = array_combine($replaces, $params);

        array_walk($transform, function ($param, $replace) use (&$path) {
            $path = str_replace($replace, $param, $path);
        });

        return $path;
    }

    public function getOptions(): array
    {
        return match ($this) {
            self::REQUEST,
            self::PAYMENT_DETAILS,
            self::REFUND,
            self::CHECK_PAYMENT_STATUS,
            self::VOID,
            self::CHECK_PRE_APPROVED_REG_KEY,
            self::EXPIRE_PRE_APPROVED_REG_KEY => [
                'connect_timeout' => 5,
                'timeout'         => 20,
            ],
            self::CONFIRM,
            self::PAY_PRE_APPROVED => [
                'connect_timeout' => 5,
                'timeout'         => 40,
            ],
            self::CAPTURE => [
                'connect_timeout' => 5,
                'timeout'         => 60,
            ],
        };
    }

    public function getMethod(): string
    {
        return match ($this) {
            self::REQUEST,
            self::CONFIRM,
            self::CAPTURE,
            self::VOID,
            self::REFUND,
            self::PAYMENT_DETAILS,
            self::PAY_PRE_APPROVED,
            self::EXPIRE_PRE_APPROVED_REG_KEY => 'POST',
            self::CHECK_PAYMENT_STATUS,
            self::CHECK_PRE_APPROVED_REG_KEY => 'GET',
        };
    }

    protected function compileParameterNames(): array
    {
        preg_match_all('/\{(.*?)}/', $this->getOriginPath(), $matches);

        return $matches;
    }

    protected function getOriginPath(): string
    {
        return match ($this) {
            self::REQUEST => '/v3/payments/request',
            self::CONFIRM => '/v3/payments/{transactionId}/confirm',
            self::CAPTURE => '/v3/payments/authorizations/{transactionId}/capture',
            self::VOID => '/v3/payments/authorizations/{transactionId}/void',
            self::REFUND => '/v3/payments/{transactionId}/refund',
            self::PAYMENT_DETAILS => '/v3/payments',
            self::CHECK_PAYMENT_STATUS => '/v3/payments/requests/{transactionId}/check',
            self::CHECK_PRE_APPROVED_REG_KEY => '/v3/payments/preapprovedPay/{regKey}/check',
            self::PAY_PRE_APPROVED => '/v3/payments/preapprovedPay/{regKey}/payment',
            self::EXPIRE_PRE_APPROVED_REG_KEY => '/v3/payments/preapprovedPay/{regKey}/expire',
        };
    }
}
