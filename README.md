## LINE Pay for Laravel

[![Tests](https://github.com/rober-song/laravel-line-pay/actions/workflows/run-tests.yml/badge.svg)](https://github.com/rober-song/laravel-line-pay/actions)

```text
試玩小套件
讓LINE Pay可以快速整合到Laravel上。
還沒寫完
```

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


### todo
- 製作type (幣別, 付款方式, 語系, 確認網址)等等
- 製作spy
