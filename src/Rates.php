<?php

namespace Webhub\Vat;

class Rates
{
    protected static $data;

    public static function current(string $territory_code) : Rate
    {
        return self::territory($territory_code)->current();
    }

    public static function territory(string $code) : Territory
    {
        $code = strtoupper(trim($code));

        return new Territory(array_filter(self::data(), function ($rate) use ($code) {
            return $rate['territory_codes'] === $code;
        }));
    }

    protected static function data() : array
    {
        if (!self::$data) {
            self::$data = require 'data.php';
        }

        return self::$data;
    }
}
