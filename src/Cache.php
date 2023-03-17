<?php declare(strict_types=1);

namespace FlareScoreboard;

/**
 * Simple file based cache
 */
class Cache
{
    static private function get_cache_dir(): string
    {
        return sys_get_temp_dir();
    }

    static private function get_cache_file(string $key): string
    {
        return self::get_cache_dir() . "/$key";
    }

    static public function set(string $key, mixed $value): void
    {
        $cacheFile = self::get_cache_file($key);
        file_put_contents($cacheFile, serialize($value));
    }

    static public function get(string $key, int $max_age): mixed
    {
        $cacheFile = self::get_cache_file($key);
        if (file_exists($cacheFile)) {
            if (filemtime($cacheFile) > (time() - $max_age)) {
                return unserialize(file_get_contents($cacheFile));
            }
        }
        return null;
    }
}