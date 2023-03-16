<?php declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use FlareScoreboard\Http;

final class HttpTest extends TestCase
{
    public function testOk(): void {
        $response = Http::get("https://iswa.gsfc.nasa.gov/IswaSystemWebApp/flarescoreboard/hapi/catalog");
        $this->assertIsString($response);
    }

    public function testNotFound(): void {
        $this->expectException(Exception::class);
        $response = Http::get("https://iswa.gsfc.nasa.gov/IswaSystemWebApp/flarescoreboard/hapi/");
    }
}