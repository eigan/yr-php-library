#### Current Forecast
```php
$yr = Yr\Yr::create("Norway/Oslo/Oslo/Oslo", "/tmp");

$forecast = $yr->getCurrentForecast();
printf("Time: %s to %s\n", $forecast->getFrom()->format("H:i"), $forecast->getTo()->format("H:i"));
printf("Temp: %s %s \n", $forecast->getTemperature(), $forecast->getTemperature('unit'));
printf("Wind: %smps %s\n", $forecast->getWindSpeed(), $forecast->getWindDirection('name'));
```
```
Time: 16:00 to 17:00
Temp: 8 celsius 
Wind: 5.7mps South-southwest
```

#### Forecasts in range / hourly forecasts
```php
$yr = Yr\Yr::create("Norway/Vestfold/Sandefjord/Sandefjord", "/tmp");

foreach($yr->getHourlyForecasts(strtotime("now"), strtotime("tomorrow")) as $forecast) {
    printf("Time: %s, %s degrees\n", $forecast->getFrom()->format("H:i"), $forecast->getTemperature());
}
```
```
Time: 16:00, 7 degrees
Time: 17:00, 7 degrees
Time: 18:00, 6 degrees
Time: 19:00, 6 degrees
[...]
```

#### Meta info
```php
$yr = Yr\Yr::create("Norway/Vestfold/Sandefjord/Sandefjord", "/tmp/");

echo "Location: " . $yr->getName() . "\n";
echo "Country: " . $yr->getCountry() . "\n";
echo "Last update: " . $yr->getLastUpdated()->format("d.m.y H:i") . "\n";
echo "Next update: " . $yr->getNextUpdate()->format("d.m.y H:i") . "\n";
```
```
Location: Sandefjord
Country: Norway
Last update: 09.03.14 10:38
Next update: 09.03.14 17:00
```

#### Textual forecasts
```php
$yr = Yr\Yr::create("Norway/Vestfold/Sandefjord/Sandefjord", "/tmp/");

foreach($yr->getTextualForecasts() as $forecast) {
    print $forecast->getTitle() . "\n";
    print $forecast->getText() . "\n\n";
}
```
```
Sunday and Monday
<strong>Østlandet og Telemark:</strong> Sørvestlig frisk bris utsatte steder, periodevis sørvestlig liten kuling på kysten. Oppholdsvær og varierende skydekke. Lokal tåke. <strong>Svenskegrensa - Stavern:</strong> Sørvestlig frisk bris 10, periodevis liten kuling 12. Skiftende skydekke, opphold. Lokal tåke. Mandag morgen forbigående litt regn. Fra mandag formiddag vestlig bris. For det meste pent vær.

Monday
<strong>Østlandet:</strong> Sørvestlig bris. Skyet og lokal tåke. Forbigående litt regn sør for Oslo tidlig på dagen. I løpet av formiddagen vestlig bris og for det meste pent vær, først i vest.

[...]
```

#### Weather Stations
```php
$yr = Yr\Yr::create("Norway/Oslo/Oslo/Oslo", "/tmp", 10, null);

$weather_stations = $yr->getWeatherStations();

foreach($weather_stations as $station) {
    print "Station: {$station->getName()}\n";
    print "Temperature: {$station->getForecast()->getTemperature()}\n";
    print "Wind Direction: {$station->getForecast()->getWindDirection()}\n";

    print "\n";
}
```
```
Station: Oslo (Blindern)
Temperature: 7.6
Wind Direction: SSW

Station: Bygdøy
Temperature: 7.9
Wind Direction: 

Station: Alna
Temperature: 8.1
Wind Direction: SSW
```