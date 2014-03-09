<?php

class YrTest extends PHPUnit_Framework_TestCase {
    function setUp() {
        $this->yr = Yr\Yr::create("Norway/Oslo/Oslo/Oslo", "/tmp");
    }

    public function testCreate() {
        $yr = Yr\Yr::create("Norway/Oslo/Oslo/Oslo", "/tmp", 10, "english");

        $this->assertInstanceOf("Yr\Yr", $yr);
    }

    public function testCreateFresh() {
        $cache_dir = "/tmp/phpyr" . time();
        mkdir($cache_dir);

        $yr = Yr\Yr::create("Norway/Oslo/Oslo/Oslo", $cache_dir, 10, "english");
        $this->assertInstanceOf("Yr\Yr", $yr); 
    }

    public function testCreateNorwegian() {
        $yr = Yr\Yr::create("Norway/Oslo/Oslo/Oslo", "/tmp", 10, "norwegian");
        $this->assertInstanceOf("Yr\Yr", $yr);
    }

    public function testCreateNewNorwegian() {
        $yr = Yr\Yr::create("Norway/Oslo/Oslo/Oslo", "/tmp", 10, "newnorwegian");
        $this->assertInstanceOf("Yr\Yr", $yr);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testCreateInvalidLocationArgument() {
        Yr\Yr::create("", "/tmp", 10, null);
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testCreateInvalidLocation2() {
        Yr\Yr::create("5855/invalid", "/tmp", 10, null);
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testCreateInvalidLocation() {
        Yr\Yr::create("Norway/Vestfold/nocity/Nocity", "/tmp", 10, null);
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testCreateNotWriteableCache() {
        Yr\Yr::create("Norway/Oslo/Oslo/Oslo", "/this/dir/does/not/exist/", 10, null);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testCreateInvalidCachePath() {
        Yr\Yr::create("Norway/Oslo/Oslo/Oslo", "", 10, null);
    }

    public function testGetHourlyForecasts() {
        $forecasts = $this->yr->getHourlyForecasts();
        $this->assertTrue(is_array($forecasts) && count($forecasts) > 0);

        $forecasts = $this->yr->getHourlyForecasts(strtotime("now"), strtotime("tomorrow"));
        $this->assertTrue(is_array($forecasts) && count($forecasts) > 0);
    }

    public function testGetPeriodicForecasts() {
        $forecasts = $this->yr->getPeriodicForecasts();
        $this->assertTrue(is_array($forecasts) && count($forecasts) > 0);

        $forecast = reset($forecasts);
        $this->assertInstanceOf("Yr\Forecast", $forecast);

        $forecasts = $this->yr->getPeriodicForecasts(strtotime("now"), strtotime("tomorrow"));
        $this->assertTrue(is_array($forecasts) && count($forecasts) > 0);

        $forecast = reset($forecasts);
        $this->assertInstanceOf("Yr\Forecast", $forecast);
    }

    public function testGetForecast() {
        $this->assertInstanceOf("Yr\Forecast", $this->yr->getForecast(strtotime("now")));
    }

    public function testGetWeatherStations() {
        $stations = $this->yr->getWeatherStations();
        $this->assertTrue(is_array($stations) && count($stations) > 0);

        $station = reset($stations);
        $this->assertInstanceOf("Yr\WeatherStation", $station);
    }

    public function testGetLocation() {
        $this->assertTrue(is_string($this->yr->getLocation("type")) && !empty($this->yr->getLocation("type")));

        $this->assertTrue($this->yr->getLocation() === "Oslo");
        $this->assertTrue($this->yr->getLocation("country") === "Norway");
    }

    public function testGetCredit() {
        $this->assertTrue(is_string($this->yr->getCreditText()) && !empty($this->yr->getCreditText()));
        $this->assertTrue(is_string($this->yr->getCreditUrl()) && !empty($this->yr->getCreditUrl()));
    }

    public function testGetLinks() {
        $this->assertTrue(is_array($this->yr->getLinks()));
    }

    public function testGetCurrentForecast() {
        $this->assertInstanceOf("Yr\Forecast", $this->yr->getCurrentForecast());
    }

    public function testGetSunrise() {
        $this->assertInstanceOf("\Datetime", $this->yr->getSunrise());
    }

    public function testGetSunset() {
        $this->assertInstanceOf("\Datetime", $this->yr->getSunset());
    }

    public function testGetNextUpdate() {
        $this->assertInstanceOf("\Datetime", $this->yr->getNextUpdate());
    }

    public function testGetLastUpdated() {
        $this->assertInstanceOf("\Datetime", $this->yr->getLastUpdated());   
    }

    public function testToArray() {
        $array = $this->yr->toArray();

        $this->assertTrue(is_array($array));
    }


    /**
     * Return the test-xml
     */
    protected function getXml() {

    }
}