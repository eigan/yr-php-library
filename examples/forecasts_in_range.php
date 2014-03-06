<?php

include __DIR__ . DIRECTORY_SEPARATOR . "../Yr/Yr.php";
include __DIR__ . DIRECTORY_SEPARATOR . "../Yr/Forecast.php";

$yr = Yr\Yr::create("Norway/Vestfold/Sandefjord/Sandefjord", "/tmp");

foreach($yr->getHourlyForecasts(strtotime("now"), strtotime("tomorrow")) as $forecast) {
	echo sprintf("Time: %s, %s degrees\n", $forecast->getFrom()->format("H:i"), $forecast->getTemperature());
}