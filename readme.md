European VAT information easily available
=====

[![Build Status](https://travis-ci.org/webhub-oss/php-vat-rates.svg?branch=master)](https://travis-ci.org/webhub-oss/php-vat-rates)
[![Latest Stable Version](https://poser.pugx.org/webhub/vat/v/stable)](https://packagist.org/packages/webhub/vat)
[![Total Downloads](https://poser.pugx.org/webhub/vat/downloads)](https://packagist.org/packages/webhub/vat)
[![License](https://poser.pugx.org/webhub/vat/license)](https://packagist.org/packages/webhub/vat)

This is a wrapper of [kdeldycke/vat-rates](https://github.com/kdeldycke/vat-rates).

Usage
---

### Installation

    composer require webhub/vat

### Basic use

```php
use Webhub\Vat\Rates;

$rate = (new Rates)->in('NL')->current()->get();

$rate->rate(); // 0.21
$rate->currencyCode(); // 'EUR'

$rate = (new Rates)->in('BE')->at('1990-01-01')->get();
$rate->rate(); // 0.12
```

### Magic methods

`Rates` proxies method calls to the underlying `Rate` if it exists and is unique. 

```php
// proxying...
(new Rates)->in('DE')->current()->rate();
// equals
(new Rates)->in('DE')->current()->get()->rate();

// non unique, throws
(new Rates)->in('FR')->rate();
```

### As array

A `Rate` implements `ArrayAccess`, so when using with for example Laravel's Collection, this is perfectly possible:

```php
collect((new Rates)->in('NL')->all())
  ->sortBy('start_date')
  ->pluck('rate', 'start_date');
```

Compiling a new dataset
---

Data is obtained from `kdeldycke/vat-rates` and written to a PHP file `data.php` that is included in `Rates`.

    composer install
    composer run build  // runs Generator::generate()
    
    
###🇪🇺