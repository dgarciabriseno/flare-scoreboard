<?php declare(strict_types=1);

namespace FlareScoreboard;
use FlareScoreboard\Exceptions\HttpException;

/**
 * Provides helper methods for HTTP requests
 */
class Http
{
    /**
     * Performs a GET request.
     * Throws an exception on failure.
     *
     * @param string $url URL to get
     * @return string Response content
     */
    public static function get(string $url): string {
        $ch = curl_init($url);
        // Tell curl_exec to return the response instead of echoing it.
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        $response_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($response_code != 200) {
            throw new HttpException("Request failed", $response);
        }
        return $response;
    }
}