<?php

#!/bin/bash
#
# This can and should only be used if you need to serve the images through HTTPS (which yr does not have)
#

#
# This script will download a lot of icons used by the yr service
# Using the same paths that yr does
#  Sun: http://symbol.yr.no/grafikk/sym/b100/01d.png
#  something..: http://symbol.yr.no/grafikk/sym/b100/mf/02n.83.png
#  Wind: http://fil.nrk.no/yr/grafikk/vindpiler/32/vindpil.0250.010.png

# ^ Where sym/b100/ is the same as $path_root and vindpiler/32/ is the same as $path_wind
#
# Methods used to get the icons:
#  Forecast::getSymbol("var") (ex "01d.png", "02n.83")
#  Forecast::getWindIconKey() (ex "0250.010")
#

$path_root = "symbols";
$path_general = $path_root.DIRECTORY_SEPARATOR;
$path_wind = $path_root.DIRECTORY_SEPARATOR."wind";
// Yr prefixes with mf, we also do this in the api (so last path have to be mf)
$path_something = $path_root.DIRECTORY_SEPARATOR."mf";

// System command to use
$downloader = "curl -O ";

// How long to sleep between download (in sec)
$sleep_time = 0;

// Setup paths
if (!is_dir($path_general)) {
    mkdir($path_general);
}
if (!is_dir($path_wind)) {
    mkdir($path_wind);
}
if (!is_dir($path_something)) {
    mkdir($path_something);
}

// General icons
$images = array("01d", "01n", "02d", "02n", "03d", "03n", "04", "05d", "05n", "06d", "06n", "07d", "07n", "08d", "08n", "09", "10", "11", "12", "13", "14", "15", "16", "17", "18", "19", "20d", "20m", "20n", "21d", "21m", "21n", "22", "23");

echo "Starting script. Will download over 1000 icons, please wait..\n";

$max = 3172;
$counter = 0;

// Downloads wind icons
// Forecast::getWindIconKey()
exec("cd $path_wind && ".$downloader."http://fil.nrk.no/yr/grafikk/vindpiler/32/vindstille.png > /dev/null 2>&1 &");
setProgress(++$counter, "wind");

for ($i = 0; $i <= 975; $i += 25) {
    $num = str_pad($i, 4, "0", STR_PAD_LEFT);

    for ($angle  = 5; $angle <= 355; $angle  += 5) {
        $angle = str_pad($angle, 3, "0", STR_PAD_LEFT);
        if (!file_exists($path_wind."/vindpil.$num.$angle.png")) {
            exec("cd $path_wind && ".$downloader."http://fil.nrk.no/yr/grafikk/vindpiler/32/vindpil.$num.$angle.png > /dev/null 2>&1 &");
        }
        sleep($sleep_time);
        setProgress(++$counter, "wind");
    }
}

// Download sun icons
// Forecast::getSymbol("var")
foreach ($images as $image) {
    exec("cd $path_general && ".$downloader."http://symbol.yr.no/grafikk/sym/b100/$image.png > /dev/null 2>&1 &");
    sleep($sleep_time);
    setProgress(++$counter, "general");
}

// Download something. No time to ivestigate proper name for these now
// Forecast::getSymbol("var")
for ($i = 1; $i <= 99; $i++) {
    $num = str_pad($i, 2, "0", STR_PAD_LEFT);
    exec("cd $path_something && ".$downloader."http://symbol.yr.no/grafikk/sym/b100/mf/03n.$num.png > /dev/null 2>&1 &");
    sleep($sleep_time);
    setProgress(++$counter, "something");

    $num = str_pad($i, 2, "0", STR_PAD_LEFT);
    exec("cd $path_something && ".$downloader."http://symbol.yr.no/grafikk/sym/b100/mf/02n.$num.png > /dev/null 2>&1 &");
    sleep($sleep_time);
    setProgress(++$counter, "something");

    exec("cd $path_something && ".$downloader."http://symbol.yr.no/grafikk/sym/b100/mf/01n.$num.png > /dev/null 2>&1 &");
    sleep($sleep_time);
    setProgress(++$counter, "something");
}

echo "\nDone! Downloaded $counter files\n";

// A function to update counter
function setProgress($current, $thing)
{
    global $max;
    echo "\r".chr(27)."[K"."Downloading... ($thing) ($current files) ".((int) (($current / $max) * 100))."%";
}
