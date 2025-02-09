## LINE Pay for Laravel

[![Tests](https://github.com/rober-song/laravel-line-pay/actions/workflows/run-tests.yml/badge.svg)](https://github.com/rober-song/laravel-line-pay/actions)

### 申請sandbox帳號
[LINE Pay Sandbox](https://developers-pay.line.me/sandbox)

### 套件安裝
引入 package 到 composer.json
```bash
composer require rober-song/laravel-line-pay
```
### Laravel
發布設定檔案
```bash
php artisan vendor:publish --provider="Rober\LinePay\LinePayProvider"
```

### 設定檔案
設定檔案在 config/line_pay.php

### 簡介
- 透過 facade `LinePay` 來使用
```php
use Rober\LinePay\LinePay;

$response = LinePay::request([
    'amount' => 100,
    'currency' => 'TWD',
    'orderId' => 'order-123'
    ]);

return $response->getInfo();
```

### Testing
```php
<?php

/**
 * fake預設回傳
 * @see \Rober\LinePay\Fakes\PaymentFake::DEFAULT_RETURN
 */
use Rober\LinePay\LinePay;


test('[api] 測試確認', function () {
    LinePay::fake()->setReturn(info: ['orderId' => 'B',]);

    // 請求自己的對應api
    $response = $this->json('POST', '/line/pay/confirm/1');
    
    // 驗證
    $response->assertJson(['orderId' => 'B']);
});

test('[api] 多個請求', function () {
    LinePay::fake()
        ->setReturn(info: ['orderId' => 'A',])
        ->setReturn(code: '1141', message: 'Payment account error');

    // 請求自己的對應api
    $response = $this->json('POST', '/line/pay/A/and/B');
    // 驗證
    $response->assertStatus(400);
});
```

### todo
- 製作type (幣別, 付款方式, 語系, 確認網址)等等
- 製作spy的assert (確認是否皆有用到設定的期望、指定對應接口)
