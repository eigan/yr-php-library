<?php

include __DIR__.DIRECTORY_SEPARATOR."..".DIRECTORY_SEPARATOR."autoload.php";

$yr = Yr\Yr::create("Norway/Vestfold/Sandefjord/Sandefjord", "/tmp");

foreach ($yr->getPeriodicForecasts() as $forecast) {
    print $forecast->getFrom()->format("H:i").": ".$forecast->getTemperature()."\n";
}
