<?php declare(strict_types=1);

namespace FlareScoreboard;
use FlareScoreboard\Http;
use FlareScoreboard\Models\Dataset;
use Exception;

/**
 * Interface for getting information from a HAPI server
 */
class HapiClient {
    protected $server;

    /**
     * @param string $server URL to hapi server
     */
    public function __construct(string $server)
    {
        $this->server = $server;
    }

    /**
     * Checks that the status in the response is good.
     *
     * @return bool True if status is ok, else false
     */
    private function hapi_status_ok(array $response): bool
    {
        assert(array_key_exists("status", $response));
        assert(array_key_exists("code", $response["status"]));
        assert(array_key_exists("message", $response["status"]));
        $status = $response['status']['code'];
        return $status >= 1200 && $status <= 1299;
    }

    /**
     * Wrapper for running get requests to a HAPI server.
     * This will throw an exception if the HAPI response status is bad.
     * If no exception is thrown, then the data is safe to access.
     *
     * @param string $url URL to get info from
     * @return object
     */
    private function hapi_http_get(string $url): array {
        // Http:get throws an exception if HTTP status is bad.
        $data = Http::get($url);
        // Json::decode throws an exception if it can't parse the response.
        $data = Json::decode($data);
        // Verify that the HAPI status is okay.
        if ($this->hapi_status_ok($data)) {
            return $data;
        } else {
            throw new Exception($data['status']['message']);
        }
    }

    /**
     * Queries the HAPI server's catalog and returns an array of Datasets
     *
     * @return Dataset[]
     */
    public function catalog(): array
    {
        $response = $this->hapi_http_get($this->server . "/catalog");
        return array_map(function (array $dataset_info) {
            return Dataset::fromArray($dataset_info);
        }, $response["catalog"]);
    }
}