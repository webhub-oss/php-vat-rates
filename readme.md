VAT Rates
=====

A PHP wrapper of [kdeldycke/vat-rates](https://github.com/kdeldycke/vat-rates).

Usage
---

```php
use Webhub\Vat\Rates;

$rate = Rates::current('NL');

$rate->rate() // 0.21
$rate->currencyCode() // 'EUR'

$rate = Rates::territory('BE')->at('1990-01-01');
$rate->rate() // 0.12
```

Generating
---

    composer install
    composer run build