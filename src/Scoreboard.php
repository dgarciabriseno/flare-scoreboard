<?php declare(strict_types=1);

namespace FlareScoreboard;

use FlareScoreboard\HapiClient;
use FlareScoreboard\Cache;
use \DateTime;
use \Exception;

class Scoreboard
{
    const DATE_FORMAT = 'Y-m-d\TG:i:s';
    const MAX_CACHE_AGE = 2592000; // 30 days in seconds

    /**
     * HAPI server interface
     */
    private HapiClient $hapiClient;

    public function __construct()
    {
        $this->hapiClient = new HapiClient("https://iswa.gsfc.nasa.gov/IswaSystemWebApp/flarescoreboard/hapi");
    }

    private static function getPredictionCacheKey(string $predictionMethod, DateTime $start, DateTime $stop): string
    {
        return "{$predictionMethod}_{$start->format(self::DATE_FORMAT)}_{$stop->format(self::DATE_FORMAT)}";
    }

    /**
     * Throws an exception if the given prediction method is not in the known prediction method list
     */
    private function validatePredictionMethod(string $predictionMethod): void
    {
        // Get the list of known methods
        $predictionMethods = $this->getRegionPredictionMethods();
        // Search for the given method in the list of known methods
        $predictionMethod = array_filter($predictionMethods, function ($dataset) use ($predictionMethod) {
            return $dataset['id'] === $predictionMethod;
        });
        // If the method wasn't found, throw an exception.
        if (count($predictionMethod) === 0) {
            throw new Exception("Prediction method not found");
        }
    }

    /**
     * Get the list of predictions for the given method over the given time range
     */
    public function getPredictions(string $predictionMethod, DateTime $start, DateTime $stop): array
    {
        // Make sure the given prediction method is valid
        $this->validatePredictionMethod($predictionMethod);

        // Attempt to load the data from cache before performing a request
        $cache_key = self::getPredictionCacheKey($predictionMethod, $start, $stop);
        $predictions = Cache::get($cache_key, self::MAX_CACHE_AGE);

        // If the data was not in the cache, request it from the hapi server.
        if (!$predictions) {
            $response = $this->hapiClient->data($predictionMethod, $start, $stop, null);
            $predictions = $response;
            // Cache the new data.
            Cache::set($cache_key, $predictions);
        }
        return $predictions;
    }

    /**
     * Get the list of region-based predictionMethods
     */
    public function getRegionPredictionMethods(): array
    {
        // Check the cache first before performing a request
        $cache_key = 'predictionMethods';
        $predictionMethods = Cache::get($cache_key, self::MAX_CACHE_AGE);
        // If the methods weren't in the cache, request them from the hapi server
        if (!$predictionMethods) {
            $catalog = $this->hapiClient->catalog();
            $predictionMethods = array_filter($catalog['catalog'], function ($dataset) {
                return str_contains($dataset['id'], 'REGIONS');
            });
            Cache::set($cache_key, $predictionMethods);
        }
        return $predictionMethods;
    }
}