<?php

namespace Tests;

use PHPUnit\Framework\TestCase;
use Webhub\Vat\AmbiguousResultException;
use Webhub\Vat\NoResultException;
use Webhub\Vat\Rate;
use Webhub\Vat\Rates;

class RatesTest extends TestCase
{
    public function testCurrent()
    {
        $rates = (new Rates)->current();

        $this->assertInstanceOf(Rates::class, $rates);
    }

    public function testAll()
    {
        $rates = (new Rates)->all();

        $this->assertIsArray($rates);

        $this->assertInstanceOf(Rate::class, current($rates));
    }

    public function testCurrentFromMultiple()
    {
        $rate = (new Rates)->in('FR')->at('2019-01-01')->get();

        $this->assertInstanceOf(Rate::class, $rate);

        $this->assertEquals(0.2, $rate->rate());
    }

    public function testTerritories()
    {
        $territories = (new Rates)->territories(true);

        $this->assertContains('FR', $territories);
        $this->assertContains('DE-78266', $territories);
        $this->assertNotContains('GR-64004', $territories);

        $territories = (new Rates)->territories(false);
        $this->assertContains('GR-64004', $territories);
    }

    public function testTerritory()
    {
        $territory = (new Rates)->in('NL');

        $this->assertGreaterThanOrEqual(10, $territory->all());
    }

    public function testNoResults()
    {
        $this->expectException(NoResultException::class);

        (new Rates)->in('XXX')->get();
    }

    public function testAmbiguousResult()
    {
        $this->expectException(AmbiguousResultException::class);

        (new Rates)->in('NL')->get();
    }
}
