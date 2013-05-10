<?php

namespace eigan\yr;

/**
 * Representing a forecast for the yr service
 * Every internal variable is public because setting the variable will faster
 */
class Forecast {

    /**
     * @var \DateTime
     */
    public $from;

    /**
     * @var \DateTime
     */    
    public $to;

    /**
     * Period of the date, will be a number from 0 to 4, where 0 is early in the morning and 4 is at night
     * @var int
     */
    public $period;

    /**
     * Typically the icon to display
     * @var array
     */
    public $symbol;

    /**
     * @var String
     */
    public $precipitation;

    /**
     * @var array
     */
    public $wind_direction;

    /**
     * @var array
     */
    public $wind_speed;

    /**
     * @var array
     */
    public $temperature;

    /**
     * @var array
     */
    public $pressure;

    /**
     * We do NOTHING
     */
    public function __construct()
    {

    }

    /**
     * Creates from simplexml object
     * @param  SimpleXMLElement $xml The xml node element
     * @return Forecast
     * @throws RunetimeException If some data is missing for xml
     */
    public static function getForecastFromXml(\SimpleXMLElement $xml)
    {
        $forecast = new Forecast();

        $data = Yr::xmlToArray($xml);

        $forecast->from = \DateTime::createFromFormat(Yr::XML_DATE_FORMAT, $data['from']);
        $forecast->to = \DateTime::createFromFormat(Yr::XML_DATE_FORMAT, $data['to']);
        $forecast->period = isset($data['period']) ? $data['period'] : "";

        if(!isset($data['symbol'], 
                  $data['precipitation'], 
                  $data['windDirection'], 
                  $data['windSpeed'], 
                  $data['temperature'], 
                  $data['pressure'])) {
            throw new RuntimeException("Missing data for forecast, skipping forecast");
        }

        $forecast->symbol         = $data['symbol'];
        $forecast->precipitation  = $data['precipitation'];
        $forecast->wind_direction = $data['windDirection'];
        $forecast->wind_speed     = $data['windSpeed'];
        $forecast->temperature    = $data['temperature'];
        $forecast->pressure       = $data['pressure'];

        return $forecast;
    }

    /**
     * The symbol have three attributes with value
     *     number
     *     name
     *     var
     *
     * Default value will send the code
     * Passing null will result in an array, containg both values
     * 
     * @return string|array default is name
     */
    public function getSymbol($key = "value") 
    {
        return isset($this->symbol[$key]) ? $this->symbol[$key] : null;
    }

    /**
     * Have no idea what this is
     * 
     * @return string 
     */
    public function getPrecipitation()
    {
        return $this->precipitation;
    }

    /**
     * The wind direction have three attributes with value
     *     deg
     *     code
     *     name
     *
     * Default value will send the code
     * Passing null will result in an array, containg both values
     * 
     * @return string|array default is code
     */
    public function getWindDirection($key = "code")
    {
        return isset($this->wind_direction[$key]) ? $this->wind_direction[$key] : $this->wind_direction;
    }

    /**
     * The wind speed have two attributes with value
     *     mps
     *     name
     *
     * Passing null will result in an array, containg both values
     * 
     * @return string|array default value is meters pr sec
     */
    public function getWindSpeed($key = "mps") {
        return isset($this->wind_speed[$key]) ? $this->wind_speed[$key] : $this->wind_speed;
    }

    /**
     * The temperatur have two attributes with value
     *     unit
     *     value
     *
     * Passing null will result in an array, containg both values
     * 
     * @return string|array see documentation
     */
    public function getTemperature($key = "value")
    {
        return isset($this->temperature[$key]) ? $this->temperature[$key] : null;
    }

    /**
     * The pressure have two attributes with value
     *     unit
     *     value
     *
     * Passing null will result in an array, containg both values
     * 
     * @return string|array see documentation
     */
    public function getPressure($key = "value") {
        return isset($this->pressure[$key]) ? $this->pressure[$key] : $this->pressure;
    }

    /**
     * Time from when the forecast begins
     * @return DateTime
     */
    public function getFrom()
    {
        return $this->from;
    }

    /**
     * @param DateTime $from
     */
    public function setFrom(DateTime $from)
    {
        $this->from = $from;
    }

    /**
     * Time for when the forecast ends
     * @return DateTime
     */
    public function getTo()
    {
        return $this->to;
    }

    /**
     * @param DateTime $to
     */
    public function setTo(DateTime $to)
    {
        $this->to = $to;
    }

    /**
     * Period of the day. This might be null in hourly
     * @return int|null
     */
    public function getPeriod()
    {
        return $this->period;
    }

    /**
     * @param int $period
     */
    public function setPeriod($period = null)
    {
        $this->period = $period;
    }
}