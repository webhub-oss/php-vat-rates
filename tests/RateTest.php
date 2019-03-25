<?php

namespace Tests;

use Carbon\Carbon;
use PHPUnit\Framework\TestCase;
use Webhub\Vat\Rates;

class RateTest extends TestCase
{
    public function testGetters()
    {
        $rate = Rates::territory('NL')->at('2000-01-01');

        $this->assertEquals('0.175', $rate->rate());
        $this->assertEquals('standard', $rate->type());
        $this->assertEquals('', $rate->description());
        $this->assertEquals('EUR', $rate->currencyCode());
        $this->assertEquals(Carbon::make('1992-10-01'), $rate->start());
        $this->assertEquals(Carbon::make('2001-01-01'), $rate->stop());
    }

    public function testArrayAccess()
    {
        $rate = Rates::territory('DE')->at('1990-01-01');

        $this->assertEquals('0.14', $rate['rate']);
    }
}
