<?php declare(strict_types=1);

namespace FlareScoreboard;

use DateTime;
use FlareScoreboard\Http;
use FlareScoreboard\Models\Dataset;
use Exception;

/**
 * Interface for getting information from a HAPI server
 */
class HapiClient {
    protected $server;

    const DATE_FORMAT = 'Y-m-d\TG:i:s';

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
     * @return array
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
     * Helper function for building URLs.
     *
     * @param string $endpoint Target HAPI endpoint
     * @param ?string $dataset Dataset to insert into the URL
     * @param ?array $parameters Parameters to insert into the URL
     * @return string URL string
     */
    private function build_url(string $endpoint, ?string $dataset = null, ?array $parameters=null): string
    {
        $url = $this->server;
        $url .= "/$endpoint";
        if ($dataset) {
            $url .= "?id=$dataset";
        }
        if ($parameters) {
            $url .= "&parameters=" . $this->url_encode_parameters($parameters);
        }
        return $url;
    }

    /**
     * Encodes an array of parameters to be used in a request
     *
     * @param ?array $parameters Array of parameters to encode
     * @return string URL Encoded string
     */
    private function url_encode_parameters(?array $parameters): string
    {
        $result = "";
        if ($parameters) {
            foreach ($parameters as $parameter) {
                $result .= $parameter . ",";
            }
            // Strip trailing comma
            $result = substr($result, 0, strlen($result) - 1);
        }
        return urlencode($result);
    }

    /**
     * Queries the HAPI server's catalog and returns an array of Datasets
     *
     * @return array Array of datasets
     */
    public function catalog(): array
    {
        return $this->hapi_http_get($this->build_url("catalog"));
    }

    /**
     * Queries the HAPI server for info on the given dataset
     *
     * @param string $id Dataset id
     * @param array $parameters A subset of the parameters to return
     * @return array Info response
     */
    public function info(string $id, ?array $parameters = null): array
    {
        return $this->hapi_http_get($this->build_url("info", $id, $parameters));
    }

    /**
     *
     */
    public function data(string $id, DateTime $start, DateTime $stop, ?array $parameters): array
    {
        $url = $this->build_url("data", $id, $parameters);
        $url .= urlencode("&start=" . $start->format(self::DATE_FORMAT));
        $url .= urlencode("&stop=" . $stop->format(self::DATE_FORMAT));
        $url .= "&format=json";
        try {
            return $this->hapi_http_get($url);
        } catch (Exception $e) {
            $url = $this->build_url("data", $id, $parameters);
            $url .= "&time.min=" . urlencode($start->format(self::DATE_FORMAT));
            $url .= "&time.max=" . urlencode($stop->format(self::DATE_FORMAT));
            $url .= "&format=json";
            return $this->hapi_http_get($url);
        }
    }
}