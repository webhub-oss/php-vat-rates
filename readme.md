VAT Rates
=====

[![Build Status](https://travis-ci.org/webhub-oss/php-vat-rates.svg?branch=master)](https://travis-ci.org/webhub-oss/php-vat-rates)

A PHP wrapper of [kdeldycke/vat-rates](https://github.com/kdeldycke/vat-rates).

Usage
---

    composer require webhub/vat

```php
<?php 
use Webhub\Vat\Rates;

$rate = Rates::current('NL');

$rate->rate(); // 0.21
$rate->currencyCode(); // 'EUR'

$rate = Rates::territory('BE')->at('1990-01-01');
$rate->rate(); // 0.12
```

A `Rate` implements `ArrayAccess`, so when using with for example Laravel's Collection, this is perfectly possible:

```php
collect(Rates::territory('NL')->all())
  ->sortBy('start_date')
  ->pluck('rate', 'start_date');
```

Generating
---

    composer install
    composer run build