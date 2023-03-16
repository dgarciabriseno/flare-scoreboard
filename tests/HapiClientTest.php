<?php declare(strict_types=1);
use PHPUnit\Framework\TestCase;
use FlareScoreboard\HapiClient;

final class HapiClientTest extends TestCase
{
    public function testInvalidServer(): void {
        $client = new HapiClient("https://iswa.gsfc.nasa.gov/IswaSystemWebApp/flarescoreboard/ha");
        $this->expectException(Exception::class);
        $client->catalog();
    }

    public function testCatalog(): void {
        $client = new HapiClient("https://iswa.gsfc.nasa.gov/IswaSystemWebApp/flarescoreboard/hapi/");
        $catalog = $client->catalog();
        $dataset = array_filter($catalog['catalog'], function (array $dataset) { return $dataset['id'] === "SIDC_Operator_FULLDISK"; });
        $this->assertEquals(1, count($dataset));
    }

    public function testInfo(): void {
        $client = new HapiClient("https://iswa.gsfc.nasa.gov/IswaSystemWebApp/flarescoreboard/hapi/");
        $info = $client->info("SIDC_Operator_FULLDISK");
        $known_parameter = array_filter($info['parameters'], function (array $parameter) { return $parameter['name'] === "start_window"; });
        $this->assertEquals(1, count($known_parameter));
    }

    public function testInfoWithParameters(): void {
        $client = new HapiClient("https://iswa.gsfc.nasa.gov/IswaSystemWebApp/flarescoreboard/hapi/");
        $info = $client->info("SIDC_Operator_FULLDISK", ["start_window", "end_window"]);
        $this->assertEquals(2, count($info['parameters']));
        $this->assertEquals("start_window", $info['parameters'][0]['name']);
    }

    public function testData(): void {
        $client = new HapiClient("https://iswa.gsfc.nasa.gov/IswaSystemWebApp/flarescoreboard/hapi/");
        $response = $client->data("SIDC_Operator_FULLDISK", new DateTime("2022-01-01T00:00:00Z"), new DateTime("2022-01-03T00:00:00Z"), ["start_window", "end_window"]);
        $records = $response['data'];
        $this->assertEquals(2, count($records));
    }
}