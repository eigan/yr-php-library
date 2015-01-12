<?php

class LocationTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->location = Yr\Yr::create("Norway/Oslo/Oslo/Oslo", "/tmp");
    }
    public function testGetHourlyForecasts()
    {
        $forecasts = $this->location->getHourlyForecasts();
        $this->assertTrue(is_array($forecasts) && count($forecasts) > 0);

        $forecasts = $this->location->getHourlyForecasts(strtotime("now"), strtotime("tomorrow"));
        $this->assertTrue(is_array($forecasts) && count($forecasts) > 0);
    }

    public function testGetPeriodicForecasts()
    {
        $forecasts = $this->location->getPeriodicForecasts();
        $this->assertTrue(is_array($forecasts) && count($forecasts) > 0);

        $forecast = reset($forecasts);
        $this->assertInstanceOf("Yr\Forecast", $forecast);

        $forecasts = $this->location->getPeriodicForecasts(strtotime("now"), strtotime("tomorrow"));
        $this->assertTrue(is_array($forecasts) && count($forecasts) > 0);

        $forecast = reset($forecasts);
        $this->assertInstanceOf("Yr\Forecast", $forecast);
    }

    public function testGetForecastAt()
    {
        $this->assertInstanceOf("Yr\Forecast", $this->location->getForecastAt(strtotime("now")));
    }

    public function testGetWeatherStations()
    {
        $stations = $this->location->getWeatherStations();
        $this->assertTrue(is_array($stations) && count($stations) > 0);

        $station = reset($stations);
        $this->assertInstanceOf("Yr\WeatherStation", $station);
    }

    public function testGetName()
    {
        $name = $this->location->getName();
        $this->assertTrue(is_string($name) && !empty($name));
    }

    public function testGetType()
    {
        $type = $this->location->getType();
        $this->assertTrue(is_string($type) && !empty($type));
    }

    public function testGetCountry()
    {
        $country = $this->location->getCountry();
        $this->assertTrue(is_string($country) && !empty($country));
    }

    public function testGetTimezone()
    {
        $timezone = $this->location->getTimezone();
        $this->assertTrue(is_string($timezone) && !empty($timezone));
    }

    public function testgetLatLong()
    {
        $latLong = $this->location->getLatLong();

        $this->assertTrue(is_array($latLong));

        $this->assertArrayHasKey("lat", $latLong);
        $this->assertArrayHasKey("long", $latLong);
    }

    public function testGetCredit()
    {
        $credit_text = $this->location->getCreditText();
        $this->assertTrue(is_string($credit_text) && !empty($credit_text));
        $credit_url = $this->location->getCreditUrl();
        $this->assertTrue(is_string($credit_url) && !empty($credit_url));
    }

    public function testGetLinks()
    {
        $this->assertTrue(is_array($this->location->getLinks()));
    }

    public function testGetCurrentForecast()
    {
        $this->assertInstanceOf("Yr\Forecast", $this->location->getCurrentForecast());
    }

    public function testGetSunrise()
    {
        $this->assertInstanceOf("\Datetime", $this->location->getSunrise());
    }

    public function testGetSunset()
    {
        $this->assertInstanceOf("\Datetime", $this->location->getSunset());
    }

    public function testGetNextUpdate()
    {
        $this->assertInstanceOf("\Datetime", $this->location->getNextUpdate());
    }

    public function testGetLastUpdated()
    {
        $this->assertInstanceOf("\Datetime", $this->location->getLastUpdated());
    }

    public function testToArray()
    {
        $array = $this->location->toArray();

        $this->assertTrue(is_array($array));
    }
}
