# European VAT information easily available

[![Build Status](https://travis-ci.org/webhub-oss/php-vat-rates.svg?branch=master)](https://travis-ci.org/webhub-oss/php-vat-rates)
[![codecov](https://codecov.io/gh/webhub-oss/php-vat-rates/branch/master/graph/badge.svg)](https://codecov.io/gh/webhub-oss/php-vat-rates)
[![Latest Stable Version](https://poser.pugx.org/webhub/vat/v/stable)](https://packagist.org/packages/webhub/vat)
[![Total Downloads](https://poser.pugx.org/webhub/vat/downloads)](https://packagist.org/packages/webhub/vat)
[![License](https://poser.pugx.org/webhub/vat/license)](https://packagist.org/packages/webhub/vat)
 
This is a wrapper of [kdeldycke/vat-rates](https://github.com/kdeldycke/vat-rates).

## 🛠 Usage

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

### Rates

A `Rates` instance is a collection of rates. 

Rates can be filtered: 

- `->in(string $territory)` a territory like `DE` or `NL`
- `->at(string|Carbon $when)` a date like `2018-01-05`, supports Carbon 
- `->current()` alias for `->at(Carbon::now())`
- `->type(string $type)` rate type, currently the database only contains _standard_ rates.

When one rate remains, the `->get() : Rate` method retrieves it, otherwise it throws. 
Obtain all rates through `->all() : array`. 

### Rate

A `Rate` has:

- `->rate() : string` decimal fraction representation of the rate, e.g. '0.20' for 20%
- `->rateType() : string` type of the rate, currently `standard`.
- `->(start|stop)Date() : Carbon` Carbon date instance of first valid day and subsequent first not-valid day.
- `->currencyCode() : string` currency like `SEK` or `EUR`
- `->description() : ?string` optional description of the rate

`Rates` proxies method calls to the underlying `Rate` if it exists and is unique. 

```php
// with ->get()
(new Rates)->in('DE')->current()->get()->rate();
// equals shorter:
(new Rates)->in('DE')->current()->rate();

// non unique
(new Rates)->in('FR')->rate();  // throws AmbiguousResultException
(new Rates)->in('XX')->get();   // throws NoResultException
```

#### Rate array access

A `Rate` implements `ArrayAccess`, so when using with for example Laravel's Collection, this is perfectly possible:

```php
collect((new Rates)->in('NL')->all())
  ->sortBy('start_date')
  ->pluck('rate', 'start_date');
```

## 📝 Compiling a new dataset

Data is obtained from `kdeldycke/vat-rates` and written to a PHP file `data.php` that is included in `Rates`.

    composer install
    composer run build  // runs Generator::generate()
    
    
<h1 align="center">🇪🇺</h1>