<?php

/**
 * Manually include classes. This is for those who do not use autoloading.
 *
 * @author Einar Gangsø
 */

$namespace = __DIR__.DIRECTORY_SEPARATOR . "Yr" . DIRECTORY_SEPARATOR;

require $namespace . "Yr.php";
require $namespace . "Location.php";
require $namespace . "Forecast.php";
require $namespace . "TextualForecast.php";
require $namespace . "WeatherStation.php";
