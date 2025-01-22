<?php

namespace Rober\LinePay\Contracts;

use Rober\LinePay\Enums\LinePayModel;

interface PaymentContract
{
    public static function createPayment(array $config, LinePayModel $model): self;
    public function request($params): ResponseContract;

    public function confirm($transactionId, $params): ResponseContract;

    public function capture($transactionId, $params): ResponseContract;

    public function void($transactionId, $params = []): ResponseContract;

    public function refund($transactionId, $params = []): ResponseContract;

    public function paymentDetails($params): ResponseContract;

    public function checkPaymentStatus($transactionId): ResponseContract;

    public function checkPreApprovedRegKey($regKey, $params = []): ResponseContract;

    public function payPreApproved($regKey, $params): ResponseContract;

    public function expirePreApprovedRegKey($regKey): ResponseContract;

    public function setNonce($nonce): self;
}
