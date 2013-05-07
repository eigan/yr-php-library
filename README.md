Yr.no PHP library
==================

What is this?
-------------
This is a php library for the norwegian wheather service yr.no. Currently not tested for production.

Todo
----
- ~~Support for the other languages (norwegian and nynorsk)~~
- More documentation
- ~~Cache lifetime~~
- Find a better name for the yr class
- Support for observations
- Support textual forecasts representations
- ~~Retrieve time for sunrise and sunset~~

Requirements
------------
- PHP 5.?
- curl

Example
----------
```php
$yr = Yr::create("Norway/Telemark/Sauherad/Gvarv", "/tmp/");

echo "Current temperature in Gvarv is " . $yr->getCurrentForecast()->getTemperature();

echo "Upcoming temperatures:";
foreach($yr->getHourlyForecasts() as $forecast) {
    print $forecast->getFrom()->format("H:i") . ": " . $forecast->getTemperature() . "\n";
}
```

Documentation
-------------
No documentation yet, please check the code.
