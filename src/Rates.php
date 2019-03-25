<?php

namespace Webhub\Vat;

use Carbon\Carbon;

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
            return in_array($code, explode("\n", $rate['territory_codes']));
        }));
    }

    public static function all() : array
    {
        return array_map(function ($rate) {
            return new Rate($rate);
        }, self::data());
    }

    public static function territories(bool $current = true) : array
    {
        $now = Carbon::now();

        $territories = [];

        foreach (self::data() as $rate) {
            if ($current) {
                if ($rate['start_date'] && $now->isBefore($rate['start_date'])) {
                    continue;
                }

                if ($rate['stop_date'] && $now->isAfter($rate['stop_date'])) {
                    continue;
                }
            }

            $territories = array_merge($territories, explode("\n", $rate['territory_codes']));
        }

        return $territories;
    }

    protected static function data() : array
    {
        if (!self::$data) {
            self::$data = require 'data.php';
        }

        return self::$data;
    }
}
