<?php

include "../Yr.php";

$yr = eigan\yr\Yr::create("Norway/Vestfold/Sandefjord/Sandefjord", "/tmp/");

$forecast = $yr->getCurrentForecast();
print $forecast->getFrom()->format("H:i") . ": " . $forecast->getTemperature();
