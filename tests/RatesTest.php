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

    public function testRates()
    {
        $rates = Rates::all();

        $this->assertIsArray($rates);

        $this->assertInstanceOf(Rate::class, current($rates));
    }

    public function testCurrentFromMultiple()
    {
        $rate = Rates::territory('FR')->at('2019-01-01');

        $this->assertInstanceOf(Rate::class, $rate);

        $this->assertEquals(0.2, $rate->rate());
    }

    public function testTerritories()
    {
        $territories = Rates::territories(true);

        $this->assertContains('FR', $territories);
        $this->assertContains('DE-78266', $territories);
        $this->assertNotContains('GR-64004', $territories);

        $territories = Rates::territories(false);
        $this->assertContains('GR-64004', $territories);
        ;
    }

    public function testTerritory()
    {
        $territory = Rates::territory('NL');

        $this->assertInstanceOf(Territory::class, $territory);

        $this->assertGreaterThanOrEqual(10, $territory->all());
    }

    public function testNoResults()
    {
        $this->expectException(NoResultException::class);

        Rates::current('XXX');
    }
}
