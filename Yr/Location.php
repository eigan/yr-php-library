<?php

namespace Yr;

class Location
{
    /**
     * @var Forecast[]
     */
    protected $forecasts_hourly;

    /**
     * Periodic data
     * @var Forecast[]
     */
    protected $forecasts_periodic;

    /**
     * @var TextualForecast[]
     */
    protected $textual_forecasts;

    /**
     * List of WheaterStation objects
     * @var WeatherStation[]
     */
    protected $weather_stations;

    /**
     * The location where we have weather data
     * @var array
     */
    protected $location;

    /**
     * @var array
     */
    protected $links;

    /**
     * Credit should be used...
     * @var String
     */
    protected $credit_url;

    /**
     * Credit derp
     * @var String
     */
    protected $credit_text;

    /**
     * Time when the web service was last refreshed
     * @var \DateTime
     */
    protected $last_update_date;

    /**
     * Time when the web service will update next
     * @var \DateTime
     */
    protected $next_update_date;

    /**
     * @var \Datetime
     */
    protected $sunrise;

    /**
     * @var \Datetime
     */
    protected $sunset;

    /**
     * Creates the Yr object with forecasts
     *
     * @param array $location
     * @param array $forecasts_periodic
     * @param array $forecasts_hourly
     */
    public function __construct(array $location, array $forecasts_periodic, array $forecasts_hourly)
    {
        $this->location             = $location;
        $this->forecasts_periodic   = $forecasts_periodic;
        $this->forecasts_hourly     = $forecasts_hourly;
        $this->links                = array();
        $this->weather_stations     = array();
    }

    /**
     * @return String
     */
    public function getName()
    {
        return $this->location['name'];
    }

    /**
     * @return String
     */
    public function getType()
    {
        return $this->location['type'];
    }

    /**
     * @return String
     */
    public function getCountry()
    {
        return $this->location['country'];
    }

    /**
     * @return String
     */
    public function getTimezone()
    {
        return $this->location['timezone']['id'];
    }

    /**
     * @return array
     */
    public function getLatLong()
    {
        return array(
            'lat' => $this->location['location']['latitude'],
            'long' => $this->location['location']['longitude'],
        );
    }

    /**
     * List of links to the yr.no frontend
     *
     * @return array
     */
    public function getLinks()
    {
        return $this->links;
    }

    /**
     * Adds a link
     *
     * @param String $name
     * @param String $url
     */
    public function addLink($name, $url)
    {
        $this->links[$name] = $url;
    }

    /**
     * Returns the current forecast (using periodic)
     *
     * @return Forecast
     */
    public function getCurrentForecast()
    {
        $forecast = reset($this->forecasts_hourly);

        return $forecast instanceof Forecast ? $forecast : null;
    }

    /**
     * Returns the upcoming forecasts as array of Forecast objects
     *
     * You can optionally specify $from and $to as unixtime.
     *
     * @param  int   $from unixtime for when the first forecast should start
     * @param  int   $to   unixtime for when the last forecast should start
     * @return array Forecast objects
     */
    public function getHourlyForecasts($from = null, $to = null)
    {
        if (!is_null($from) || !is_null($to)) {
            return $this->getForecastsBetweenTime($this->forecasts_hourly, $from, $to);
        }

        return $this->forecasts_hourly;
    }

    /**
     * There is 4 peridos in a day. You can check the Forecast::getPeriod()
     *
     * You can optionally specify $from and $to as unixtime.
     *
     * @param  int   $from unixtime for when the first forecast should start
     * @param  int   $to   unixtime for when the last forecast should start
     * @return array Forecast objects
     */
    public function getPeriodicForecasts($from = null, $to = null)
    {
        if (!is_null($from) || !is_null($to)) {
            return $this->getForecastsBetweenTime($this->forecasts_periodic, $from, $to);
        }

        return $this->forecasts_periodic;
    }

    /**
     * Get a Forecast at a specific time
     *
     * @param  String     $time unixtime for when the forecast should be
     * @return Forecast[]
     */
    public function getForecastAt($time)
    {
        $forecasts = $this->getForecastsBetweenTime($this->forecasts_hourly, $time);

        return reset($forecasts);
    }

    /**
     * Internal function to find forecasts between a given time
     *
     * Notice that if $from is null, we change it to now()
     * and if $to is null, we change it to the time one year from now
     *
     * @param  Forecast[] $forecasts the list of forecasts to check
     * @param  int        $from      unixtime for when the forecast should start
     * @param  int        $to        unixtime for when the last forecast should start
     * @return array      list of matching forecasts
     */
    protected function getForecastsBetweenTime($forecasts, $from, $to = null)
    {
        $result = array();

        // Check for null, or non valid unixtimes
        $from = is_null($from) || !is_int($from) ? time() : $from;
        $to = is_null($to) || !is_int($to) ? strtotime("1 year") : $to;

        foreach ($forecasts as $forecast) {
            if ($forecast->getFrom()->getTimestamp() >= $from &&
                $forecast->getFrom()->getTimestamp() <= $to) {
                $result[] = $forecast;
            }
        }

        return $result;
    }

    /**
     * Note: The textual forecasts is always norwegian..
     * Note: Places outside of Norway might not have textual forecasts
     *
     * @return TextualForecast[]
     */
    public function getTextualForecasts()
    {
        return $this->textual_forecasts;
    }

    /**
     * @param TextualForecast[] $forecasts
     */
    public function setTextualForecasts(array $forecasts)
    {
        $this->textual_forecasts = $forecasts;
    }

    /**
     * Note: Places outside of Norway might not have weather stations
     *
     * @return WeatherStation[]
     */
    public function getWeatherStations()
    {
        return $this->weather_stations;
    }

    /**
     * @param WeatherStation[] $weather_stations
     */
    public function setWeatherStations($weather_stations)
    {
        $this->weather_stations = $weather_stations;
    }

    /**
     * @return \Datetime
     */
    public function getSunrise()
    {
        return $this->sunrise;
    }

    /**
     * @param \Datetime
     */
    public function setSunrise(\Datetime $time)
    {
        $this->sunrise = $time;
    }

    /**
     * @return \Datetime
     */
    public function getSunset()
    {
        return $this->sunset;
    }

    /**
     * @param \Datetime $time
     */
    public function setSunset(\Datetime $time)
    {
        $this->sunset = $time;
    }

    /**
     * Returns the time the hourly data was last updated
     * @return \DateTime
     */
    public function getLastUpdated()
    {
        return $this->last_update_date;
    }

    /**
     * Setter for last update
     * @param \DateTime $date
     */
    public function setLastUpdated(\Datetime $date)
    {
        $this->last_update_date = $date;
    }

    /**
     * Returns the time this will update next time the hourly data will update
     * @return \DateTime
     */
    public function getNextUpdate()
    {
        return $this->next_update_date;
    }

    /**
     *
     * @param \DateTime
     */
    public function setNextUpdate(\Datetime $date)
    {
        $this->next_update_date = $date;
    }

    /**
     * @return String
     */
    public function getCreditUrl()
    {
        return $this->credit_url;
    }

    /**
     * @param String $url
     */
    public function setCreditUrl($url)
    {
        $this->credit_url = $url;
    }

    /**
     * You have to display this text with a link to the creditUrl! Read rules
     * @see getCreditUrl()
     * @return String
     */
    public function getCreditText()
    {
        return $this->credit_text;
    }

    /**
     * @param String $text
     */
    public function setCreditText($text)
    {
        $this->credit_text = $text;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return array(
            'location'    => $this->location,
            'links'       => $this->links,
            'last_update' => $this->getLastUpdated(),
            'next_update' => $this->getNextUpdate(),
            'credit_text' => $this->getCreditText(),
            'credit_url'  => $this->getCreditUrl(),
            'sunrise'     => $this->getSunrise(),
            'sunset'      => $this->getSunset(),
            'forecasts'   => $this->getHourlyForecasts(),
            'weather_stations' => $this->getWeatherStations(),
        );
    }
}
