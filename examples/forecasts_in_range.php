<?php

include "../Yr.php";

$yr = eigan\yr\Yr::create("Norway/Vestfold/Sandefjord/Sandefjord", "/tmp");

foreach($yr->getHourlyForecasts(strtotime("now"), strtotime("tomorrow")) as $forecast) {
	echo sprintf("Time: %s, %s degrees\n", $forecast->getFrom()->format("H:i"), $forecast->getTemperature());
}