<?php

namespace Yr;

/**
 * Representing a forecast for the yr service
 * Please do not use the public vars directly.
 *
 * @author  Einar Gangsø <einargangso@gmail.com>
 */
class Forecast
{
    /**
     * From datetime.
     *
     * @var \DateTime
     */
    public $from;

    /**
     * To datetime.
     *
     * @var \DateTime
     */
    public $to;

    /**
     * Period of the date, will be a number from 0 to 4
     * where 0 is early in the morning and 4 is at night.
     *
     * @var int
     */
    public $period;

    /**
     * Typically the icon to display.
     *
     * @var array
     */
    public $symbol;

    /**
     * Precipitation in millimeters.
     *
     * @var String
     */
    public $precipitation;

    /**
     * Data for wind direction.
     *
     * @var array
     */
    public $wind_direction;

    /**
     * Data for wind speed.
     *
     * @var array
     */
    public $wind_speed;

    /**
     * Data for temperature.
     *
     * @var array
     */
    public $temperature;

    /**
     * Data for pressure.
     *
     * @var array
     */
    public $pressure;

    /**
     * We do NOTHING.
     */
    public function __construct()
    {
    }

    /**
     * Creates from simplexml object.
     *
     * @param \SimpleXMLElement $xml The xml node element
     *
     * @return Forecast
     *
     * @throws \RuntimeException If some data is missing for xml
     */
    public static function getForecastFromXml(\SimpleXMLElement $xml)
    {
        $forecast = new Forecast();

        $data = Yr::xmlToArray($xml);

        if (!isset($data['from'], $data['to'])) {
            throw new \RuntimeException("Missing from/to for forecast");
        }

        $forecast->from = \DateTime::createFromFormat(Yr::XML_DATE_FORMAT, $data['from']);
        $forecast->to = \DateTime::createFromFormat(Yr::XML_DATE_FORMAT, $data['to']);
        $forecast->period = isset($data['period']) ? $data['period'] : "";

        if (!isset($data['symbol'],
                  $data['precipitation'],
                  $data['windDirection'],
                  $data['windSpeed'],
                  $data['temperature'],
                  $data['pressure'])) {
            throw new \RuntimeException("Missing data for forecast");
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
     * The symbol have four attributes with value
     *     number
     *     numberEx
     *     name     [default]
     *     var.
     *
     * Default value will give "name"
     *
     * @param String $key number|name|var
     *
     * @return string|array default is name
     */
    public function getSymbol($key = "name")
    {
        return isset($this->symbol[$key]) ? $this->symbol[$key] : null;
    }

    /**
     * @param array $symbol
     */
    public function setSymbol($symbol)
    {
        $this->symbol = $symbol;
    }

    /**
     * The symbol can have three attributes with value
     *     value [default]
     *     minvalue
     *     maxvalue.
     *
     * Default value will give "value"
     *
     * @param String $key value|minvalue|maxvalue
     *
     * @return string
     */
    public function getPrecipitation($key = "value")
    {
        return isset($this->precipitation[$key]) ? $this->precipitation[$key] : null;
    }

    /**
     * @param String $precipitation
     */
    public function setPrecipitation($precipitation)
    {
        $this->precipitation = $precipitation;
    }

    /**
     * The wind direction have three attributes with value
     *     deg
     *     code [default]
     *     name.
     *
     * Default value will send the code
     *
     * @param String $key deg|code|name
     *
     * @return string|array default is code
     */
    public function getWindDirection($key = "code")
    {
        return isset($this->wind_direction[$key]) ? $this->wind_direction[$key] : null;
    }

    /**
     * @param array $wind_direction
     */
    public function setWindDirection($wind_direction)
    {
        $this->wind_direction = $wind_direction;
    }

    /**
     * The wind speed have two attributes with value
     *     mps [default]
     *     name.
     *
     * @param String $key mps|name
     *
     * @return string|array default value is meters pr sec
     */
    public function getWindSpeed($key = "mps")
    {
        return isset($this->wind_speed[$key]) ? $this->wind_speed[$key] : null;
    }

    /**
     * @param array $wind_speed
     */
    public function setWindSpeed($wind_speed)
    {
        $this->wind_speed = $wind_speed;
    }

    /**
     * Utility method to get the filename for the arrows (speed and direction).
     *
     * http://fil.nrk.no/yr/grafikk/vindpiler/32/vindpil.{$speed}.{$degree}.png
     *
     * So you can use the icon like so:
     * http://fil.nrk.no/yr/grafikk/vindpiler/32/vindpil.{$forecast->getWindIconKey()}.png
     *
     * if it returns 0, then it should be "vindstille" (no wind) http://fil.nrk.no/yr/grafikk/vindpiler/32/vindstille.png
     *
     * @returm string
     */
    public function getWindIconKey()
    {
        $speed = (round(($this->getWindSpeed("mps")/2.5)) * 2.5) * 10;
        $speed = str_pad($speed, 4, '0', STR_PAD_LEFT);

        // 2 and down is 0 speed - vindstille
        if ($this->getWindSpeed() <= 0.2) {
            return 0;
        }

        $degree = round((($this->getWindDirection("deg")/10) * 2) / 2) * 10;
        $degree = str_pad($degree, 3, '0', STR_PAD_LEFT);

        // 360 degree is 0
        if ($degree >= 360) {
            $degree = 0;
        }

        return "$speed.$degree";
    }

    /**
     * The temperatur have two attributes with value
     *     unit
     *     value [default].
     *
     * @param String $key value|unit
     *
     * @return string|array see documentation
     */
    public function getTemperature($key = "value")
    {
        return isset($this->temperature[$key]) ? $this->temperature[$key] : null;
    }

    /**
     * @param array $temperature
     */
    public function setTemperature($temperature)
    {
        $this->temperature = $temperature;
    }

    /**
     * The pressure have two attributes with value
     *     unit
     *     value [default].
     *
     * @param String $key value|unit
     *
     * @return string|array see documentation
     */
    public function getPressure($key = "value")
    {
        return isset($this->pressure[$key]) ? $this->pressure[$key] : null;
    }

    /**
     * @param array $pressure
     */
    public function setPressure($pressure)
    {
        $this->pressure = $pressure;
    }

    /**
     * Time from when the forecast begins.
     *
     * @return \DateTime
     */
    public function getFrom()
    {
        return $this->from;
    }

    /**
     * @param \DateTime $from
     */
    public function setFrom(\DateTime $from)
    {
        $this->from = $from;
    }

    /**
     * Time for when the forecast ends.
     *
     * @return \DateTime
     */
    public function getTo()
    {
        return $this->to;
    }

    /**
     * @param \DateTime $to
     */
    public function setTo(\DateTime $to)
    {
        $this->to = $to;
    }

    /**
     * Period of the day. This might be null in hourly.
     *
     * @return int|null
     */
    public function getPeriod()
    {
        return strlen($this->period) > 0 ? $this->period : null;
    }

    /**
     * @param int $period
     */
    public function setPeriod($period)
    {
        $this->period = $period;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return array(
            'from' => $this->getFrom(),
            'to'   => $this->getTo(),
            'symbol' => $this->symbol,
            'temperature' => $this->temperature,
            'wind_speed' => $this->wind_speed,
            'wind_direction' => $this->wind_direction,
            'period' => $this->period,
        );
    }
}
