<?php

include __DIR__ . DIRECTORY_SEPARATOR . "../Yr/Yr.php";
include __DIR__ . DIRECTORY_SEPARATOR . "../Yr/Forecast.php";

$yr = Yr\Yr::create("Norway/Vestfold/Sandefjord/Sandefjord", "/tmp/");

echo "Location: " . $yr->getLocation() . "\n";
echo "Last update: " . $yr->getLastUpdated()->format("d.m.y H:i") . "\n";
echo "Next update: " . $yr->getNextUpdate()->format("d.m.y H:i") . "\n";