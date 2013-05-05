<?php

include "../Yr.php";

$yr = eigan\yr\Yr::create("Norway/Vestfold/Sandefjord/Sandefjord", "/tmp/");

foreach($yr->getHourlyForecasts() as $forecast) {
	print $forecast->getFrom()->format("H:i") . ": " . $forecast->getTemperature() . "\n";
}

