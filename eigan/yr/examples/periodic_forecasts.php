<?php

include "../Yr.php";
include "../Forecast.php";

$yr = eigan\yr\Yr::create("Norway/Vestfold/Sandefjord/Sandefjord", "/tmp");

foreach($yr->getPeriodicForecasts() as $forecast) {
    print $forecast->getFrom()->format("H:i") . ": " . $forecast->getTemperature() . "\n";
}

