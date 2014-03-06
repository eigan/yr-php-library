Yr.no PHP library
==================
PHP library for the norwegian wheather service yr.no. Currently in use on a fairly large norwegian site.

## Todo
- Support for observations
- Support textual forecasts representations

### Requirements
- PHP 5.3

## Installation
#### With [composer](https://getcomposer.org/)
    composer require eigan/yr
#### Without composer
Download [Yr.php](https://raw.github.com/eigan/yr-php-library/master/Yr/Yr.php) and [Example.php](https://raw.github.com/eigan/yr-php-library/master/Yr/Forecast.php). Add them to a place on you server and include them in your script.


## Examples
These examples require you to already have Yr.php (and Forecast.php) included in your code.

#### Current forecast
```php
$yr = Yr\Yr::create("Norway/Vestfold/Sandefjord/Sandefjord", "/tmp");

$forecast = $yr->getCurrentForecast();
echo sprintf("Time: %s to %s\n", $forecast->getFrom()->format("H:i"), $forecast->getTo()->format("H:i"));
echo sprintf("Temp: %s %s\n", $forecast->getTemperature(), $forecast->getTemperature('unit'));
echo sprintf("Wind: %smps %s\n", $forecast->getWindSpeed(), $forecast->getWindDirection('name'));
```

#### Forecasts tomorrow
```php
$yr = Yr\Yr::create("Norway/Vestfold/Sandefjord/Sandefjord", "/tmp");

foreach($yr->getPeriodicForecasts(strtotime("tomorrow"), strtotime("midnight second day") - 1/*sec*/) as $forecast) {
    echo sprintf("Time: %s, %s degrees\n", $forecast->getFrom()->format("H:i"), $forecast->getTemperature());
}
```

#### Hourly forecasts rest of the day

```php
$yr = Yr\Yr::create("Norway/Vestfold/Sandefjord/Sandefjord", "/tmp");

foreach($yr->getHourlyForecasts(strtotime("now"), strtotime("tomorrow") - 1/*sec*/) as $forecast) {
    echo sprintf("Time: %s, %s degrees\n", $forecast->getFrom()->format("H:i"), $forecast->getTemperature());
}
```


## Documentation
### Yr
```php
/**
 * 
 * Please read the rules for using the yr api http://om.yr.no/verdata/vilkar/
 * This class will implement caching for you
 * 
 * @see http://om.yr.no/verdata/free-weather-data/
 * @author Einar Gangsø <einargangso@gmail.com>
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
     * @param String the location, like Norway/Vestfold/Sandefjord
     * @return Yr
     * @throws RuntimeException if cache path is not writeable
     * @throws RuntimeException if the location is not correct
     */
    public static function create($location, $cache_path, $cache_life = 10, $language = "english");

    /**
     * Returns the location name
     * You can specifiy other output by the $key 
     * Available keys are:
     *     name
     *     type
     *     country
     * 
     * @param String $key name, type or country
     * @return String
     */
    public function getLocation($key = "name");

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
     * @return \Datetime
     */
    public function getSunrise();

    /**
     * @param \Datetime
     */
    public function setSunrise(\Datetime $time);

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
    public function getCreditText();
}
```

### Forecast
```php
/**
 * Representing a forecast for the yr service
 * Every internal variable is public because setting the variable will be faster
 */
class Forecast {

    /**
     * The symbol have three attributes with value
     *     number
     *     name
     *     var
     *
     * Default value will give "number"
     * Passing null will result in an array, containg both values
     * 
     * @return string|array default is name
     */
    public function getSymbol($key = "name");

    /**
     * The symbol can have three attributes with value
     *     value
     *     minvalue
     *     maxvalue
     *
     * Default value will give "value"
     * Passing null will result in an array, containg both values
     * 
     * @return string 
     */
    public function getPrecipitation($key = "value");

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
    public function getWindDirection($key = "code");

    /**
     * The wind speed have two attributes with value
     *     mps
     *     name
     *
     * Passing null will result in an array, containg both values
     * 
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
     *     value
     *
     * Passing null will result in an array, containg both values
     * 
     * @return string|array see documentation
     */
    public function getTemperature($key = "value");

    /**
     * The pressure have two attributes with value
     *     unit
     *     value
     *
     * Passing null will result in an array, containg both values
     * 
     * @return string|array see documentation
     */
    public function getPressure($key = "value");

    /**
     * Time from when the forecast begins
     * @return DateTime
     */
    public function getFrom();

    /**
     * @param DateTime $from
     */
    public function setFrom(DateTime $from);

    /**
     * Time for when the forecast ends
     * @return DateTime
     */
    public function getTo();

    /**
     * @param DateTime $to
     */
    public function setTo(DateTime $to);

    /**
     * Period of the day. This might be null in hourly
     * @return int|null
     */
    public function getPeriod();

    /**
     * @param int $period
     */
    public function setPeriod($period = null);
}
```

## License
MIT http://www.tldrlegal.com/license/mit-license