<?php

#!/bin/bash
#
# This can and should only be used if you need to serve the images from your own server
#

#
# This script will download a lot of icons used by the yr service
# Using the same paths that yr does
#  Sun: https://symbol.yr.no/grafikk/sym/b100/01d.png
#  something..: https://symbol.yr.no/grafikk/sym/b100/mf/02n.83.png
#  Wind: https://fil.nrk.no/yr/grafikk/vindpiler/32/vindpil.0250.010.png

# ^ Where sym/b200/ is the same as $path_root and vindpiler/32/ is the same as $path_wind
#
# Methods used to get the icons:
#  Forecast::getSymbol("var") (ex "01d.png", "02n.83")
#  Forecast::getWindIconKey() (ex "0250.010")
#

$path_root = "symbols";
$path_general = $path_root . DIRECTORY_SEPARATOR;
$path_wind = $path_root . DIRECTORY_SEPARATOR . "wind";
// src prefixes with mf, we also do this in the api (so last path have to be mf)
$path_moon = $path_root . DIRECTORY_SEPARATOR . "mf";

// System command to use
$downloader = "curl -O";

// How many microseconds to sleep between each download (1000000 = 1s)
$sleep_time = 10000; // default 0.01s - we don't want to DDoS yr (it's still a pretty fast script)

// Setup paths
if (!is_dir($path_general)) {
    mkdir($path_general);
}
if (!is_dir($path_wind)) {
    mkdir($path_wind);
}
if (!is_dir($path_moon)) {
    mkdir($path_moon);
}

// General icons
$images = array(
    "01d",
    "01m",
    "02d",
    "02m",
    "03d",
    "03m",
    "04",
    "05d",
    "05m",
    "06d",
    "06m",
    "07d",
    "07m",
    "08d",
    "08m",
    "09",
    "10",
    "11",
    "12",
    "13",
    "14",
    "15",
    "20d",
    "20m",
    "21d",
    "21m",
    "22",
    "23",
    "30",
    "31",
    "32",
    "33",
    "34",
    '46',
    '47',
    '48',
    '49',
    '50',
);

$images_mf = array(
    '01n',
    '02n',
    '03n',
    '05n',
    '06n',
    '07n',
    '08n',
    '40n',
    '41n',
    '42n',
    '43n',
    '44n',
    '45n',
);

$max = 4291;

echo "Downloading $max icons, please wait...\n";

$counter = 0;

// Downloads wind icons
// Forecast::getWindIconKey()
exec("cd $path_wind && $downloader https://fil.nrk.no/yr/grafikk/vindpiler/32/vindstille.png > /dev/null 2>&1 &");
setProgress(++$counter, "wind");

for ($i = 0; $i <= 1000; $i += 25) {
    $num = str_pad($i, 4, "0", STR_PAD_LEFT);

    for ($angle_deg = 0; $angle_deg <= 355; $angle_deg += 5) {
        $angle = str_pad($angle_deg, 3, "0", STR_PAD_LEFT);
        exec("cd $path_wind && $downloader https://fil.nrk.no/yr/grafikk/vindpiler/32/vindpil.$num.$angle.png > /dev/null 2>&1 &");
        setProgress(++$counter, "wind");
    }
}

// Download sun icons
// Forecast::getSymbol("var")
foreach ($images as $image) {
    exec("cd $path_general && $downloader https://symbol.yr.no/grafikk/sym/b200/$image.png > /dev/null 2>&1 &");
    setProgress(++$counter, "sun");
}

// Download moon icons
// Forecast::getSymbol("var")
for ($i = 0; $i <= 99; $i++) {
    $num = str_pad($i, 2, "0", STR_PAD_LEFT);
    foreach ($images_mf as $image_mf) {
        exec("cd $path_moon && $downloader https://symbol.yr.no/grafikk/sym/b200/mf/$image_mf.$num.png > /dev/null 2>&1 &");
        setProgress(++$counter, "moon");
    }
}

echo "\nDone! Downloaded $counter files\n";

// A function to update counter
function setProgress($current, $thing)
{
    global $max;
    global $sleep_time;
    echo "\r" . chr(27) . "[K" . "Downloading... ($thing) ($current files) " . ((int)(($current / $max) * 100)) . "%";
    usleep($sleep_time);
}
