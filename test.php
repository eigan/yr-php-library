<?php
/**
 * Test suitcase for the yr library
 * include it in the terminal
 *
 * $ php -a
 * php > include "test.php";
 */

// Include the classfiles once
include_once "Yr/Yr.php";
include_once "Yr/Forecast.php";

// This is not set by default in ini (OSX),
// so override to standard norwegian to avoid warnings
date_default_timezone_set("Europe/Oslo");

// Saving you
set_error_handler(function ($errno, $msg) {
    // Just print the error
    echo "PHP Error: ".$msg."\n";

    // Do not go to internal php error handler
    return true;
});

// Possible to override default values
$location = !isset($location) ? "Norway/Vestfold/Sandefjord/Sandefjord" : $location;
$cache_path = !isset($cache_path) ? "/tmp" : $cache_path;
$cache_life = !isset($cache_life) ? 10 : $cache_life;
$language = !isset($language) ? "english" : $language;

// Initialize yr object
$yr = Yr\Yr::create($location, $cache_path, $cache_life, $language);

// Notice user if the yr object was created
if ($yr instanceof Yr\Yr) {
    print "An Yr object is now available on ".'$yr';
}
