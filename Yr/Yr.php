<?php

namespace Yr;

/**
 *
 * Please read the rules for using the yr api http://om.yr.no/verdata/vilkar/
 * This class will implement caching for you
 *
 * @package Yr
 * @see http://om.yr.no/verdata/free-weather-data/
 * @author Einar Gangsø <einargangso@gmail.com>
 */
class Yr
{
    /**
     * This is the format used in the xml files.
     * It is converted to DateTime everywhere
     * @var string
     */
    const XML_DATE_FORMAT = "Y-m-d?H:i:s";

    /**
     * Yr url
     */
    const API_URL = "http://www.yr.no/";

    /**
     * HTTP 200 response with text/xml
     */
    const SERVICE_OK = 1;

    /**
     * HTTP 200 with text/html, or HTTP 404
     */
    const SERVICE_LOCATION_INVALID = 5;

    /**
     * HTTP 500, or no response
     */
    const SERVICE_UNKNOWN_STATE = 10;

    const CACHE_PREFIX = "phpyrno_";

    /**
     */
    protected function __construct()
    {
    }

    /**
     * This method builds the Yr object from the freely available Yr api
     *
     * Notice that you have to be very specific about the location. Use the same location as you will find
     * on the yr.no site. For instance:
     *
     * This is the URL for the town Sandefjord:
     *     http://www.yr.no/place/Norway/Vestfold/Sandefjord/Sandefjord/
     * Which with this library would be:
     *     Yr::create("Norway/Vestfold/Sandefjord/Sandefjord")
     *
     *
     * @todo  Check data we are setting on the yr object (meta data, dates, etc)
     * @param  String                    $location   the location, like Norway/Vestfold/Sandefjord
     * @param  String                    $cache_path where to store the cache
     * @param  int                       $cache_life life of the cache
     * @param  String                    $language   language, norwegian or english
     * @return Yr\Location
     * @throws \RuntimeException         if cache path is not writeable
     * @throws \RuntimeException         if the location is not correct
     * @throws \InvalidArgumentException
     */
    public static function create($location, $cache_path, $cache_life = 10, $language = "english")
    {
        if (!isset($location) || empty($location)) {
            throw new \InvalidArgumentException("Location need to be set");
        }

        if (!isset($cache_path) || empty($cache_path)) {
            throw new \InvalidArgumentException("Cache path need to be set");
        }

        // Get url, different for each language
        $baseurl = self::getApiUrlByLanguage($language);

        // Clean the cache path
        $cache_path = realpath($cache_path).DIRECTORY_SEPARATOR;

        // Check if cache path is readable
        if (!is_writable($cache_path)) {
            throw new \RuntimeException("Cache path ($cache_path) is not writable");
        }

        // Cache paths for the location
        $xml_periodic_path = $cache_path.self::CACHE_PREFIX.md5($baseurl.$location)."_periodic.xml";
        $xml_hourly_path = $cache_path.self::CACHE_PREFIX.md5($baseurl.$location)."_hourly.xml";

        // Check response from web service
        // This is a critical process if we have no cache. Please see Yr::getUrlResponseCode() for explanation
        if (!is_readable($xml_periodic_path) || !is_readable($xml_hourly_path)) {
            $test = self::getServiceResponseCode($baseurl.$location);

            if ($test == self::SERVICE_LOCATION_INVALID) {
                throw new \RuntimeException("The location ($location) is wrong. Please check Yr::create() documentation.", 1);
            } elseif ($test == self::SERVICE_UNKNOWN_STATE) {
                throw new \RuntimeException("Could not connect to yr service. Tried the url for 7 times, but did not work. Might be do to invalid location, or yr service is down.");
            }
        }

        // Download the periodic xml if we doesnt have it
        if (!is_readable($xml_periodic_path) || (time() - filemtime($xml_periodic_path) > ($cache_life * 60))) {
            $xml_content = fopen("$baseurl/$location/forecast.xml", 'r');

            if (!empty($xml_content)) {
                // Only cache if there is content from request (is empty when no internet connection)
                file_put_contents($xml_periodic_path, $xml_content);
            }
        }

        // Download the hourly xml if we doesnt have it
        if (!is_readable($xml_hourly_path) || (time() - filemtime($xml_hourly_path) > ($cache_life * 60))) {
            $xml_content = fopen("$baseurl/$location/forecast_hour_by_hour.xml", 'r');

            if (!empty($xml_content)) {
                // Only cache if there is content from request (is empty when no internet connection)
                file_put_contents($xml_hourly_path, $xml_content);
            }
        }

        $xml_hourly = new \SimpleXMLElement($xml_hourly_path, null, true);
        $xml_periodic = new \SimpleXMLElement($xml_periodic_path, null, true);

        // Get all the hourly forecasts and create Forecast objects
        $forecasts_hourly = array();
        foreach ($xml_hourly->forecast->tabular->time as $forecast) {
            try {
                $forecasts_hourly[] = Forecast::getForecastFromXml($forecast);
            } catch (\RuntimeException $e) {
            } // Skip those we cant create..
        }

        // Get all the periodic forecasts and create Forecast objects
        $forecasts_periodic = array();
        foreach ($xml_periodic->forecast->tabular->time as $forecast) {
            try {
                $forecasts_periodic[] = Forecast::getForecastFromXml($forecast);
            } catch (\RuntimeException $e) {
            } // Skip those we cant create..
        }

        $textual_forecasts = array();

        // Some places to not have textual forecasts
        if (!empty($xml_hourly->forecast->text)) {
            foreach ($xml_hourly->forecast->text->location->time as $forecast) {
                try {
                    $textual_forecasts[] = TextualForecast::createTextualForecastFromXml($forecast);
                } catch (\Exception $e) {
                } // Skip those we cant create..
            }
        }

        // weather_stations
        $weather_stations = array();
        if (!empty($xml_hourly->observations)) {
            foreach ($xml_hourly->observations->weatherstation as $observation) {
                try {
                    $weather_stations[] = WeatherStation::getWeatherStationFromXml($observation);
                } catch (\Exception $e) {
                } // Skip those we cant create..
            }
        }

        // Get other data for our object
        $location = self::xmlToArray($xml_periodic->location);
        $links    = self::xmlToArray($xml_periodic->links);
        $credit   = self::xmlToArray($xml_periodic->credit->link);
        $meta     = self::xmlToArray($xml_periodic->meta);
        $sun      = self::xmlToArray($xml_periodic->sun);

        // Set the data on the object
        try {
            $yr = new Location($location, $forecasts_periodic, $forecasts_hourly);

            $yr->setWeatherStations($weather_stations);
            $yr->setTextualForecasts($textual_forecasts);

            if (isset($links['link'])) {
                foreach ($links['link'] as $link) {
                    $yr->addLink($link['id'], $link['url']);
                }
            }

            if (isset($credit['text'], $credit['url'])) {
                $yr->setCreditText($credit['text']);
                $yr->setCreditUrl($credit['url']);
            }

            $yr->setLastUpdated(\DateTime::createFromFormat(self::XML_DATE_FORMAT, $meta['lastupdate']));
            $yr->setNextUpdate(\DateTime::createFromFormat(self::XML_DATE_FORMAT, $meta['nextupdate']));

            if (isset($sun['set'], $sun['rise'])) {
                $yr->setSunset(\DateTime::createFromFormat(self::XML_DATE_FORMAT, $sun['set']));
                $yr->setSunrise(\DateTime::createFromFormat(self::XML_DATE_FORMAT, $sun['rise']));
            }

            // Finally return the object
            return $yr;
        } catch (\Exception $e) {
            // We fall back and send exception if something goes wrong
            throw new \RuntimeException("Could not create Location object");
        }
    }

    /**
     * Utility method to get water temperatures
     *
     */
    public function getWaterTemperatures()
    {
    }

    /**
     * Converts xml to array and hide comments
     * @param  \SimpleXMLElement $data
     * @param  array             $out
     * @return array
     */
    public static function xmlToArray($data, $out = array())
    {
        foreach ((array) $data as $index => $node) {
            if ($index == 'comment') {
                continue;
            }

            if ($index == '@attributes') {
                $out = array_merge($out, is_object($node) || is_array($node) ? self::xmlToArray($node) : (string) $node);
            } else {
                $out[$index] = is_object($node) || is_array($node) ? self::xmlToArray($node) : (string) $node;
            }
        }

        return $out;
    }

    /**
     * @param  String $language lowercase language string
     * @return String
     */
    private static function getApiUrlByLanguage($language)
    {
        switch ($language) {
            case "norwegian":
                return self::API_URL."sted/";
            break;

            case "newnorwegian":
            case "neonorwegian":
            case "nynorsk":
                return self::API_URL."sted/";
            break;

            default:
                return self::API_URL."place/";
            break;
        }
    }

    /**
     * Checks the response from yr service
     * @see getUrlResponseCode()
     * @param  array $url the urls
     * @return int   the response
     */
    private static function getServiceResponseCode($url)
    {
        // Check first url
        $url1 = self::getUrlResponseCode($url."/forecast_hour_by_hour.xml");

        // If the url is ok, test the other one
        if ($url1 == self::SERVICE_OK) {
            $url2 = self::getUrlResponseCode($url."/forecast.xml");

            // Since url1 is ok, return code for url2
            return $url2;
        }

        return $url1;
    }

    /**
     * There has been found a bug in the yr service that will deny access to the xml files.
     * This method tries to work out this issue
     *
     * The problem is that if you try a city that has not been visited for a while, you will
     * get HTTP response 500 from yr. This will go away after 5-10 requests to yr. So we
     * will need to send at least 5 requests if we get HTTP 500.
     *
     * Thanks to https://github.com/prebenlm for finding the bug
     *
     * @param  String $url full url to the endpoint
     * @return int    Status code
     */
    private static function getUrlResponseCode($url)
    {
        for ($i = 0; $i < 7; $i++) {
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_NOBODY, true);
            curl_exec($ch);
            $retcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $type = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);
            curl_close($ch);

            if ($retcode === 0) {
                throw new \RuntimeException("Check your internet connection");
            }

            // This might happend for 5-10 times
            // just skipping to next request if this does happen
            if ($retcode == 500) {
                continue;
            }

            // Response is OK, but we are not returning XML
            // Most likely malformatted url, like: Norway/Akershus/Nes
            if (($retcode == 200 && $type != "text/xml; charset=utf-8")
                || $retcode == 404) {
                return self::SERVICE_LOCATION_INVALID;
            }

            // Response is OK, and the format is xml. Lets go with that
            if ($retcode == 200 && $type == "text/xml; charset=utf-8") {
                return self::SERVICE_OK;
            }
        }

        return self::SERVICE_UNKNOWN_STATE;
    }
}
