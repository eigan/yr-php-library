[![Build Status](https://travis-ci.org/eigan/yr-php-library.png?branch=master)](https://travis-ci.org/eigan/yr-php-library)

Yr.no PHP library
==================
PHP library for the norwegian wheather service yr.no. Currently in use on a fairly large norwegian site.

### Status
Please note that I am not adding more feature at this time (other than accepting pull requests). Consider using something like forecast.io which has lots more data.

### Requirements
- PHP 5.3

## Installation
#### With [composer](https://getcomposer.org/)
    composer require eigan/yr
#### Without composer
Clone, or [download the repo as zip](https://github.com/eigan/yr-php-library/archive/master.zip). Place it somewhere on your server. Then include the autoload.php file in your code.

Remember to set `date.timezone = "Europe/Oslo"` in php.ini or whatever is your timezone.

## Changelog
**09 march 2014**
- ! Yr::create() now returns a Location object
- ! removed getLocation, use getName(), getCountry(), getType() instead
- ! Forecast methods will no longer return the array when specifying null as parameter. Try to use toArray() instead
- Added autoload.php to make it easier to load classes if/when structure changes.
- Added Forecast->toArray()
- Added Location->toArray()
- Added WeatherStations (observations)
- Added TextualForecasts
- Added Location->getForecastAt($time), get a forecast at a specific unixtime
- Added tests. 98.15% code coverage (coverage cant hit when yr return HTTP 500)
- More exceptions and error checks in all classes
- phpdoc cleanup

## Examples
These examples require you to already have included autoload.php (or using an autoloader)

#### Current forecast
```php
$yr = Yr\Yr::create("Norway/Vestfold/Sandefjord/Sandefjord", "/tmp");

$forecast = $yr->getCurrentForecast();
printf("Time: %s to %s\n", $forecast->getFrom()->format("H:i"), $forecast->getTo()->format("H:i"));
printf("Temp: %s %s\n", $forecast->getTemperature(), $forecast->getTemperature('unit'));
printf("Wind: %smps %s\n", $forecast->getWindSpeed(), $forecast->getWindDirection('name'));
```
```
Time: 16:00 to 17:00
Temp: 8 celsius 
Wind: 5.7mps South-southwest
```

#### Forecasts tomorrow
```php
$yr = Yr\Yr::create("Norway/Vestfold/Sandefjord/Sandefjord", "/tmp");

foreach($yr->getPeriodicForecasts(strtotime("tomorrow"), strtotime("midnight second day") - 1/*sec*/) as $forecast) {
    printf("Time: %s, %s degrees\n", $forecast->getFrom()->format("H:i"), $forecast->getTemperature());
}
```
```
Time: 00:00, 5 degrees
Time: 06:00, 2 degrees
Time: 12:00, 8 degrees
Time: 18:00, 7 degrees
```

#### Hourly forecasts rest of the day

```php
$yr = Yr\Yr::create("Norway/Vestfold/Sandefjord/Sandefjord", "/tmp");

foreach($yr->getHourlyForecasts(strtotime("now"), strtotime("tomorrow") - 1/*sec*/) as $forecast) {
    printf("Time: %s, %s degrees\n", $forecast->getFrom()->format("H:i"), $forecast->getTemperature());
}
```
```
Time: 20:00: 5 degrees
Time: 21:00: 4 degrees
Time: 22:00: 3 degrees
Time: 23:00: 3 degrees
```

See more examples in [examples directory](https://github.com/eigan/yr-php-library/tree/master/examples).

## Documentation
### Yr
```php
/**
 * 
 * Please read the rules for using the yr api http://om.yr.no/verdata/vilkar/
 * This class will implement caching for you
 *
 * @see http://om.yr.no/verdata/free-weather-data/
 */
class Yr {

    /**
     * This method builds the Yr object from the freely available Yr api
     *
     * Notice that you have to be very specific about the location. Use the same location as you will find 
     * on the yr.no site. For instance:
     *
     * This is the URL for the town Sandefjord:
     *     http://www.yr.no/place/Norway/Vestfold/Sandefjord/Sandefjord/
     * Which with this library would be:
     *     Yr::create("Norway/Vestfold/Sandefjord/Sandefjord")
     * 
     * 
     * @todo  Check data we are setting on the yr object (meta data, dates, etc)
     * @param String $location    the location, like Norway/Vestfold/Sandefjord
     * @param String $cache_path  where to store the cache
     * @param int    $cache_life  life of the cache
     * @param String $language    language, norwegian or english
     * @return Yr\Location
     * @throws \RuntimeException if cache path is not writeable
     * @throws \RuntimeException if the location is not correct
     * @throws \InvalidArgumentException
     */
    public static function create($location, $cache_path, $cache_life = 10, $language = "english");
}
```

### Location
```php
class Location {

    /**
     * @return String
     */
    public function getName();

    /**
     * @return String
     */
    public function getType();

    /**
     * @return String
     */
    public function getCountry();
    /**
     * @return String
     */
    public function getTimezone();

    /**
     * @return array
     */
    public function getLatLong();

    /**
     * List of links to the yr.no frontend
     * 
     * @return array
     */
    public function getLinks();

    /**
     * Returns the current forecast (using periodic)
     *
     * @return Forecast
     */
    public function getCurrentForecast();

    /**
     * Returns the upcoming forecasts as array of Forecast objects
     *
     * You can optionally specify $from and $to as unixtime. 
     * 
     * @param int $from unixtime for when the first forecast should start
     * @param int $to unixtime for when the last forecast should start
     * @return array Forecast objects
     */
    public function getHourlyForecasts($from = null, $to = null);

    /**
     * There is 4 peridos in a day. You can check the Forecast::getPeriod()
     *
     * You can optionally specify $from and $to as unixtime. 
     * 
     * @param int $from unixtime for when the first forecast should start
     * @param int $to unixtime for when the last forecast should start
     * @return array Forecast objects
     */
    public function getPeriodicForecasts($from = null, $to = null);

    /**
     * Get a Forecast at a specific time
     *
     * @param String $time unixtime for when the forecast should be
     * @return Forecast[]
     */
    public function getForecastAt($time);

    /**
     * Note: Th textual forecasts is always norwegian..
     * @return TextualForecast[]
     */
    public function getTextualForecasts();

    /**
     * @return WeatherStation[]
     */
    public function getWeatherStations();

    /**
     * @return \Datetime
     */
    public function getSunrise();

    /**
     * @return \Datetime
     */
    public function getSunset();
    /**
     * Returns the time the hourly data was last updated
     * @return \DateTime
     */
    public function getLastUpdated();

    /**
     * Returns the time this will update next time the hourly data will update
     * @return \DateTime
     */
    public function getNextUpdate();
    /**
     * @return String 
     */
    public function getCreditUrl();

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
     * @return array 
     */
    public function toArray();
}
```

### Forecast
```php
/**
 * Representing a forecast for the yr service
 *
 */
class Forecast {

    /**
     * The symbol have three attributes with value
     *     number [default]
     *     name
     *     var
     *
     * Default value will give "number"
     *
     * @param String $key number|name|var
     * @return string|array default is name
     */
    public function getSymbol($key = "name");

    /**
     * The symbol can have three attributes with value
     *     value [default]
     *     minvalue
     *     maxvalue
     *
     * Default value will give "value"
     *
     * @param String $key value|minvalue|maxvalue
     * @return string 
     */
    public function getPrecipitation($key = "value");


    /**
     * The wind direction have three attributes with value
     *     deg
     *     code [default]
     *     name
     *
     * Default value will send the code
     *
     * @param String $key deg|code|name
     * @return string|array default is code
     */
    public function getWindDirection($key = "code");


    /**
     * The wind speed have two attributes with value
     *     mps [default]
     *     name
     *
     * @param String $key mps|name
     * @return string|array default value is meters pr sec
     */
    public function getWindSpeed($key = "mps");

    /**
     * Utility method to get the filename for the arrows (speed and direction)
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
    public function getWindIconKey();

    /**
     * The temperatur have two attributes with value
     *     unit
     *     value [default]
     *
     * @param String $key value|unit
     * @return string|array see documentation
     */
    public function getTemperature($key = "value");

    /**
     * The pressure have two attributes with value
     *     unit
     *     value [default]
     *
     * @param String $key value|unit
     * @return string|array see documentation
     */
    public function getPressure($key = "value");


    /**
     * Time from when the forecast begins
     * @return \DateTime
     */
    public function getFrom();

    /**
     * Time for when the forecast ends
     * @return \DateTime
     */
    public function getTo();

    /**
     * Period of the day. This might be null in hourly
     * @return int|null
     */
    public function getPeriod();

    /**
     * @return array
     */
    public function toArray();
}
```

### TextualForecast
```php
/**
 * Textual forecasts for a given day (or two days)
 * Note: There is some html inside the getText()...
 *
 */
class TextualForecast {

    /**
     * @return String
     */
    public function getTitle();

    /**
     * @return String
     */
    public function getText();

    /**
     * @return \Datetime
     */
    public function getFrom();
    
    /**
     * @return \DateTime
     */
    public function getTo();
}
```

### WeatherStation
```php
/**
 * Weather Station
 * Note that the Forecast object will not be complete since all data from it might not be set :/
 *
 */
class WeatherStation {

    /**
     * @return String
     */
    public function getName();

    /**
     * @return numeric
     */
    public function getDistance();

    /**
     * array('lat' => '[xx.xxxx]', 'long' => '[xx.xxxx]')
     *
     * @return array
     */
    public function getLatLong();

    /**
     * 
     * @return String
     */
    public function getSource();

    /**
     * Warning: Not everything will be set on this object
     *
     * @return Forecast the current forecast reported by this weatherstation
     */
    public function getForecast();

    /**
     * @return array
     */
    public function toArray();
}
```

## License
MIT http://www.tldrlegal.com/license/mit-license
