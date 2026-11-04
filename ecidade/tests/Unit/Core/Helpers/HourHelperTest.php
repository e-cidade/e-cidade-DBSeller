<?php


namespace Tests\Unit\Core\Helpers;


use ECidade\Core\Helpers\HourHelper;
use Tests\TestCase;

class HourHelperTest extends TestCase
{
    /**
     * @var HourHelper
     */
    private $helper;

    protected function setUp(): void
    {
        parent::setUp();
        $this->helper = new HourHelper();
    }

    public function testShouldConvertFloatToHour()
    {
        self::assertEquals('02:30', $this->helper->convertFloatToHour(2.5));
        self::assertEquals('02:00', $this->helper->convertFloatToHour(2));
        self::assertEquals('24:00', $this->helper->convertFloatToHour(24));
        self::assertEquals('55:00', $this->helper->convertFloatToHour(55));
        self::assertEquals('55:59', $this->helper->convertFloatToHour(55.98));
        self::assertEquals('12:34', $this->helper->convertFloatToHour(12.57));
        self::assertEquals('00:00', $this->helper->convertFloatToHour(0));
    }

    public function testShouldConvertHourToFloat()
    {
        self::assertEquals(2.5, $this->helper->convertHourToFloat('02:30'));
        self::assertEquals(2, $this->helper->convertHourToFloat('02:00'));
        self::assertEquals(24, $this->helper->convertHourToFloat('24:00'));
        self::assertEquals(55, $this->helper->convertHourToFloat('55:00'));
        self::assertEquals(55.98, $this->helper->convertHourToFloat('55:59'));
        self::assertEquals(12.57, $this->helper->convertHourToFloat('12:34'));
        self::assertEquals(0, $this->helper->convertHourToFloat('00:00'));
    }
}
