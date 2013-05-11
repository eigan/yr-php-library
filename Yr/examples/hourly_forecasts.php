<?php

include "../Yr.php";
include "../Forecast.php";

$yr = Yr\Yr::create("Norway/Vestfold/Sandefjord/Sandefjord", "/tmp");

foreach($yr->getHourlyForecasts() as $forecast) {
    print $forecast->getFrom()->format("H:i") . ": " . $forecast->getTemperature() . "\n";
}

