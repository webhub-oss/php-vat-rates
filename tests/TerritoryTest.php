<?php

namespace Tests;

use PHPUnit\Framework\TestCase;
use Webhub\Vat\Rate;
use Webhub\Vat\Rates;
use Webhub\Vat\Territory;

class TerritoryTest extends TestCase
{
    public function testRates()
    {
        $territory = Rates::territory('BE');

        $this->assertInstanceOf(Territory::class, $territory);

        $this->assertCount(7, $territory->rates());

        $this->assertInstanceOf(Rate::class, current($territory->rates()));
    }

    /**
     * @throws \Webhub\Vat\AmbiguousResultException
     * @throws \Webhub\Vat\NoResultException
     */
    public function testCurrent()
    {
        $current = Rates::territory('BE')->current();

        $this->assertInstanceOf(Rate::class, $current);

        $this->assertEquals('0.21', $current->rate());
        $this->assertEquals('standard', $current->type());
        $this->assertEquals('EUR', $current->currencyCode());
        $this->assertEquals('Belgium (member state) standard VAT rate.', $current->description());
    }

    /**
     * @throws \Webhub\Vat\AmbiguousResultException
     * @throws \Webhub\Vat\NoResultException
     */
    public function testAt()
    {
        $at = Rates::territory('BE')->at('1990-01-01');

        $this->assertEquals("0.19", $at->rate());
    }
}
