<?php

include __DIR__ . DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR . "autoload.php";

$yr = Yr\Yr::create("Norway/Oslo/Oslo/Oslo", "/tmp");

$forecast = $yr->getCurrentForecast();
echo sprintf("Time: %s to %s\n", $forecast->getFrom()->format("H:i"), $forecast->getTo()->format("H:i"));
echo sprintf("Temp: %s %s \n", $forecast->getTemperature(), $forecast->getTemperature('unit'));
echo sprintf("Wind: %smps %s\n", $forecast->getWindSpeed(), $forecast->getWindDirection('name'));