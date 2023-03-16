<?php declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use FlareScoreboard\Json;
use Exception;

final class JsonTest extends TestCase
{
    public function testGoodDecode(): void {
        $json = "{\"hello\": 1}";
        $decoded = Json::decode($json);
        $this->assertEquals(1, $decoded['hello']);
    }

    public function testBadDecode(): void {
        $this->expectException(Exception::class);
        $bad_json = "This is not JSON";
        Json::decode($bad_json);
    }

    public function testGoodEncode(): void {
        $arr = array("hello" => 1);
        $encoded = Json::encode($arr);
        $this->assertEquals("{\"hello\":1}", $encoded);
    }

    public function testBadEncode(): void {
        // TODO: Not sure how to make a bad json object
    }
}