<?php

namespace Tests;

use PHPUnit\Framework\TestCase;
use Webhub\Vat\NoResultException;
use Webhub\Vat\Rate;
use Webhub\Vat\Rates;
use Webhub\Vat\Territory;

class RatesTest extends TestCase
{
    public function testCurrent()
    {
        $rate = Rates::current('NL');

        $this->assertInstanceOf(Rate::class, $rate);

        $this->assertEquals("0.21", $rate->rate());
        $this->assertEquals('standard', $rate->type());
    }

    public function testTerritory()
    {
        $territory = Rates::territory('NL');

        $this->assertInstanceOf(Territory::class, $territory);

        $this->assertCount(10, $territory->rates());
    }

    public function testNoResults()
    {
        $this->expectException(NoResultException::class);

        Rates::current('XXX');
    }
}
