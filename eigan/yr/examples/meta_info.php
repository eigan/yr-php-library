<?php

include "../Yr.php";
include "../Forecast.php";

$yr = eigan\yr\Yr::create("Norway/Vestfold/Sandefjord/Sandefjord", "/tmp/");

echo "Location: " . $yr->getLocation() . "\n";
echo "Last update: " . $yr->getLastUpdated()->format("d.m.y H:i") . "\n";
echo "Next update: " . $yr->getNextUpdate()->format("d.m.y H:i") . "\n";