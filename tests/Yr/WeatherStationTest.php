<?php

class WeatherStationTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->yr = Yr\Yr::create("Norway/Oslo/Oslo/Oslo", "/tmp");
        $stations = $this->yr->getWeatherStations();
        $this->station = reset($stations);
    }

    public function testGetName()
    {
        $name = $this->station->getName();
        $this->assertTrue(is_string($name) && !empty($name), "getName() is empty or not string");
    }

    public function testGetDistance()
    {
        $distance = $this->station->getDistance();
        $this->assertTrue(is_numeric($distance) && !empty($distance), "getDistance() is empty or not numeric");
    }

    public function testgetLatLong()
    {
        $latLong = $this->station->getLatLong();

        $this->assertTrue(is_array($latLong));

        $this->assertArrayHasKey("lat", $latLong);
        $this->assertArrayHasKey("long", $latLong);
    }

    public function testGetSource()
    {
        $source = $this->station->getSource();

        $this->assertTrue(is_string($source) && !empty($source), "getSource() is not string or empty");
    }

    public function testGetForecast()
    {
        $forecast = $this->station->getForecast();

        $this->assertInstanceOf("Yr\Forecast", $forecast);
    }

    public function testSetForecast()
    {
        $forecast = new Yr\Forecast();
        $this->station->setForecast($forecast);

        $this->assertEquals($forecast, $this->station->getForecast());
    }

    public function testToArray()
    {
        $array = $this->station->toArray();

        $this->assertTrue(is_array($array) && count($array) > 3);

        $this->assertArrayHasKey('name', $array);
    }
}
