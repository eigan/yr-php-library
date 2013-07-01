Yr.no PHP library
==================

What is this?
-------------
This is a php library for the norwegian wheather service yr.no. Currently in use on a fairly large norwegian site.

Todo
----
- Support for observations
- Support textual forecasts representations

Requirements
------------
- PHP 5.3
- curl

Examples
----------

### Current forecast
```php
$yr = Yr\Yr::create("Norway/Vestfold/Sandefjord/Sandefjord", "/tmp");

$forecast = $yr->getCurrentForecast();
echo sprintf("Time: %s to %s\n", $forecast->getFrom()->format("H:i"), $forecast->getTo()->format("H:i"));
echo sprintf("Temp: %s %s \n", $forecast->getTemperature(), $forecast->getTemperature('unit'));
echo sprintf("Wind: %smps %s\n", $forecast->getWindSpeed(), $forecast->getWindDirection('name'));
```
### Forecasts tomorrow
```php
$yr = Yr\Yr::create("Norway/Vestfold/Sandefjord/Sandefjord", "/tmp");

foreach($yr->getPeriodicForecasts(strtotime("tomorrow"), strtotime("midnight second day") - 1) as $forecast) {
    echo sprintf("Time: %s, %s degrees\n", $forecast->getFrom()->format("H:i"), $forecast->getTemperature());
}
```

### Hourly forecasts rest of the day
```php
$yr = Yr\Yr::create("Norway/Vestfold/Sandefjord/Sandefjord", "/tmp");

foreach($yr->getHourlyForecasts(strtotime("now"), strtotime("tomorrow") - 1) as $forecast) {
    echo sprintf("Time: %s, %s degrees\n", $forecast->getFrom()->format("H:i"), $forecast->getTemperature());
}
```

License
----------
MIT http://www.tldrlegal.com/license/mit-license