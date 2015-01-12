<?php

include __DIR__.DIRECTORY_SEPARATOR."..".DIRECTORY_SEPARATOR."autoload.php";

$yr = Yr\Yr::create("Norway/Vestfold/Sandefjord/Sandefjord", "/tmp/");

echo "Location: ".$yr->getName()."\n";
echo "Country: ".$yr->getCountry()."\n";
echo "Last update: ".$yr->getLastUpdated()->format("d.m.y H:i")."\n";
echo "Next update: ".$yr->getNextUpdate()->format("d.m.y H:i")."\n";
