## LINE Pay for Laravel

```text
試玩小套件
讓LINE Pay可以快速整合到Laravel上。
還沒寫完
```

### 套件安裝
引入 package 到 composer.json
```bash
composer require rober/laravel-line-pay
```

### Laravel
發布設定檔案
```bash
php artisan vendor:publish --provider="Rober\LinePay\LinePayProvider"
```

### 設定檔案
設定檔案在 config/line_pay.php
