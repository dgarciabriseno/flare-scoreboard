<?php declare(strict_types=1);

namespace FlareScoreboard;

use Exception;

/**
 * Wrappers for JSON encoding/decoding. Throws exceptions on failure.
 */
class Json
{
    /**
     * Equivalent to json_decode, with exceptions
     */
    public static function decode(string $json): array
    {
        return json_decode($json, true, 512, JSON_THROW_ON_ERROR);
    }

    /**
     * Equivalent to json_encode, with exceptions
     */
    public static function encode(array $data)
    {
        return json_encode($data, JSON_THROW_ON_ERROR);
    }
}