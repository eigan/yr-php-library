<?php

class TextualForecastTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->yr = Yr\Yr::create("Norway/Oslo/Oslo/Oslo", "/tmp");
        $forecasts = $this->yr->getTextualForecasts();
        $this->forecast = reset($forecasts);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testInvalidForecast()
    {
        new Yr\TextualForecast("", "", new \Datetime());
    }

    public function testGetTitle()
    {
        $title = $this->forecast->getTitle();

        $this->assertTrue(is_string($title) && !empty($title));
    }

    public function testGetText()
    {
        $text = $this->forecast->getText();

        $this->assertTrue(is_string($text) && !empty($text));
    }

    public function testGetFrom()
    {
        $from = $this->forecast->getFrom();
        $this->assertInstanceOf("\Datetime", $from);
    }

    public function testGetTo()
    {
        $to = $this->forecast->getTo();

        $this->assertInstanceOf("\Datetime", $to);
    }
}
