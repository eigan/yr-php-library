Yr.no PHP library
==================

What is this?
-------------
This is a php library stuff for the norwegian wheather system yr.no. 

Todo
----
Support for the other languages (norwegian and nynorsk)

Requirements
------------
PHP 5.?

How to use
----------
    <?php
    $yr = Yr::create("Norway/Telemark/Sauherad/Gvarv", "/tmp/");
    
    echo "Current temperatur in Gvarv is " . $yr->getCurrentForecast()->getTemperature();
    
    echo "Upcoming temps:";
    foreach($yr->getHourlyForecasts() as $forecast) {
        print $forecast->getFrom()->format("H:i") . ": " . $forecast->getTemperature() . "\n";
    }


Documentation
-------------
No documentation site yet, please check the code