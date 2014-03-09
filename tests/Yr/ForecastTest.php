<?php

class ForecastTest extends PHPUnit_Framework_TestCase {
    function setUp() {
        $this->yr = Yr\Yr::create("Norway/Oslo/Oslo/Oslo", "/tmp");
        $this->forecast = $this->yr->getCurrentForecast();
    }

    public function testIsForecast() {
        $this->assertInstanceOf("Yr\Forecast", $this->forecast);
    }

    /**
     * Testing missing data from xml
     *
     * @expectedException \RuntimeException
     */
    public function testGetForecastFromXmlInvalid() {
        Yr\Forecast::getForecastFromXml(new \SimpleXMLElement('      <time from="2014-03-07T10:00:00" to="2014-03-07T11:00:00">
        <!-- Valid from 2014-03-07T10:00:00 to 2014-03-07T11:00:00 -->
        <symbol number="9" name="Rain" var="09" />
        <precipitation value="0.3" minvalue="0.1" maxvalue="0.6" />
        <temperature unit="celsius" value="5" />
        <pressure unit="hPa" value="1011.7" />
      </time>'));
    }

    /**
     * Testing missing data from xml
     *
     * @expectedException \RuntimeException
     */
    public function testGetForecastFromXmlInvalid2() {
        Yr\Forecast::getForecastFromXml(new \SimpleXMLElement('      <time >
        <!-- Valid from 2014-03-07T10:00:00 to 2014-03-07T11:00:00 -->
        <symbol number="9" name="Rain" var="09" />
        <precipitation value="0.3" minvalue="0.1" maxvalue="0.6" />
        <temperature unit="celsius" value="5" />
        <pressure unit="hPa" value="1011.7" />
      </time>'));
    }

    public function testGetSymbol() {
        $this->assertTrue(is_string($this->forecast->getSymbol()) && !empty($this->forecast->getSymbol()));
        $this->assertTrue(is_string($this->forecast->getSymbol("number")) && !empty($this->forecast->getSymbol("number")));
        $this->assertTrue(is_string($this->forecast->getSymbol("name")) && !empty($this->forecast->getSymbol("name")));
        $this->assertTrue(is_string($this->forecast->getSymbol("var")) && !empty($this->forecast->getSymbol("var")));
    }

    public function testGetPrecipitation() {
        $precipitation = $this->forecast->getPrecipitation();
        $this->assertTrue(!empty($precipitation) || $precipitation === "0");
    }

    public function testSetPrecipitation() {
        $testArray = array("value" => "1", 'minvalue' => 1, 'maxvalue' => 1);
        $this->forecast->setPrecipitation($testArray);

        $gotArray = $this->forecast->getPrecipitation();

        $this->assertEquals($this->forecast->getPrecipitation("value"), $testArray['value']);
        $this->assertEquals($this->forecast->getPrecipitation("minvalue"), $testArray['minvalue']);
        $this->assertEquals($this->forecast->getPrecipitation("maxvalue"), $testArray['maxvalue']);
    }

    public function testGetWindDirection() {
        $this->assertTrue(is_string($this->forecast->getWindDirection()) && !empty($this->forecast->getWindDirection()));
        $this->assertTrue(is_string($this->forecast->getWindDirection("deg")) && !empty($this->forecast->getWindDirection("deg")));
        $this->assertTrue(is_string($this->forecast->getWindDirection("code")) && !empty($this->forecast->getWindDirection("code")));
        $this->assertTrue(is_string($this->forecast->getWindDirection("name")) && !empty($this->forecast->getWindDirection("name")));
    }

    public function testGetTemperature() {
        $this->assertTrue(is_string($this->forecast->getTemperature()) && !empty($this->forecast->getTemperature()));
        $this->assertTrue(is_string($this->forecast->getTemperature("unit")) && !empty($this->forecast->getTemperature("unit")));
        $this->assertTrue(is_string($this->forecast->getTemperature("value")) && !empty($this->forecast->getTemperature("value")));
    }

    public function testGetPressure() {
        $this->assertTrue(is_string($this->forecast->getPressure()) && !empty($this->forecast->getPressure()));
        $this->assertTrue(is_string($this->forecast->getPressure("unit")) && !empty($this->forecast->getPressure("unit")));
        $this->assertTrue(is_string($this->forecast->getPressure("value")) && !empty($this->forecast->getPressure("value")));
    }

    public function testSetPressure() {
        $testArray = array("unit" => "m", 'value' => 1);
        $this->forecast->setPressure($testArray);

        $gotArray = $this->forecast->getPressure();

        $this->assertEquals($this->forecast->getPressure("unit"),$testArray['unit']);
        $this->assertEquals($this->forecast->getPressure("value"),$testArray['value']);
    }
    
    public function testGetWindIconKey() {
        $icon = $this->forecast->getWindIconKey();
        $this->assertTrue(!empty($icon) && is_string($icon));
    }

    public function testGetWindIconKey0() {
        $this->forecast->setWindSpeed(array('mps' => 0.2, 'name' => "slow"));

        $this->assertEquals(0, $this->forecast->getWindIconKey());
    }

    public function testGetPeriod() {
        $this->assertTrue(is_numeric($this->forecast->getPeriod()) || is_null($this->forecast->getPeriod()));
    }

    public function testSetPeriod() {
        $this->forecast->setPeriod(1);
        $this->assertEquals(1, $this->forecast->getPeriod());
    }

    public function testSetFrom() {
        $date = new \DateTime();
        $this->forecast->setFrom($date);

        $this->assertEquals($date, $this->forecast->getFrom());
    }

    public function testSetTo() {
        $date = new \DateTime();
        $this->forecast->setTo($date);

        $this->assertEquals($date, $this->forecast->getTo());
    }

    public function testToArray() {
        $array = $this->forecast->toArray();

        $this->assertTrue(is_array($array));
    }
}