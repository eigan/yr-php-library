<?php

namespace Yr;

/**
 * Weather Station
 * Note that the Forecast object will not be complete since all data from it might not be set :/
 *
 * @package Yr
 * @author Einar Gangsø <einargangso@gmail.com>
 */
class WeatherStation
{
    /**
     * @var String
     */
    protected $name;

    /**
     * @var int
     */
    protected $distance;

    /**
     * @var array
     */
    protected $latLong;

    /**
     * @var String
     */
    protected $source;

    /**
     * @var Forecast
     */
    protected $forecast;

    /**
     * @param $name
     * @param $distance
     * @param array $latLong
     * @param $source
     */
    public function __construct($name, $distance, array $latLong, $source)
    {
        $this->name = $name;
        $this->distance = (int) $distance;
        $this->latLong = $latLong;
        $this->source = $source;

        $this->forecast = new Forecast();
    }

    /**
     * @param  \SimpleXMLElement $xml
     * @return WeatherStation
     */
    public static function getWeatherStationFromXml(\SimpleXMLElement $xml)
    {
        $data = Yr::xmlToArray($xml);

        $name     = $data['name'];
        $distance = $data['distance'];
        $latLong  = array('lat' => $data['lat'], 'long' => $data['lon']);
        $source   = $data['source'];

        $station = new WeatherStation($name, $distance, $latLong, $source);

        $forecast = $station->getForecast();

        if (isset($data['symbol'])) {
            $forecast->setSymbol($data['symbol']);
        }

        if (isset($data['temperature'])) {
            $forecast->setTemperature($data['temperature']);
        }

        if (isset($data['windDirection'])) {
            $forecast->setWindDirection($data['windDirection']);
        }

        if (isset($data['windSpeed'])) {
            $forecast->setWindSpeed($data['windSpeed']);
        }

        return $station;
    }

    /**
     * @return String
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return int
     */
    public function getDistance()
    {
        return $this->distance;
    }

    /**
     * array('lat' => '[xx.xxxx]', 'long' => '[xx.xxxx]')
     *
     * @return array
     */
    public function getLatLong()
    {
        return $this->latLong;
    }

    /**
     *
     * @return String
     */
    public function getSource()
    {
        return $this->source;
    }

    /**
     * Warning: Not everything will be set on this object
     *
     * @return Forecast the current forecast reported by this weatherstation
     */
    public function getForecast()
    {
        return $this->forecast;
    }

    /**
     * @param Forecast $forecast
     */
    public function setForecast(Forecast $forecast)
    {
        $this->forecast = $forecast;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return array(
            'name' => $this->name,
            'distance' => $this->distance,
            'latLong' => $this->latLong,
            'source' => $this->source,
            'forecast' => $this->forecast,
        );
    }
}
