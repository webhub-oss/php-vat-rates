<?php

namespace Tests;

use Carbon\Carbon;
use PHPUnit\Framework\TestCase;
use Webhub\Vat\Rates;

class RateTest extends TestCase
{
    public function testGetters()
    {
        $rate = (new Rates)->in('NL')->at('2000-01-01')->get();

        $this->assertEquals('0.175', $rate->rate());
        $this->assertEquals('standard', $rate->rateType());
        $this->assertEquals('', $rate->description());
        $this->assertEquals('EUR', $rate->currencyCode());
        $this->assertEquals(Carbon::make('1992-10-01'), $rate->start());
        $this->assertEquals(Carbon::make('2001-01-01'), $rate->stop());
    }

    public function testArrayAccess()
    {
        $rate = (new Rates)->in('DE')->at('1990-01-01')->get();

        $this->assertEquals('0.14', $rate['rate']);

        $this->assertTrue(isset($rate['rate']));
        $this->assertFalse(isset($rate['foo']));

        $rate['rate'] = 0.123;
        $this->assertEquals(0.123, $rate['rate']);

        unset($rate['rate']);
        $this->assertFalse(isset($rate['rate']));
    }
}
