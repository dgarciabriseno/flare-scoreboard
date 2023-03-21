<?php declare(strict_types=1);
use PHPUnit\Framework\TestCase;
use FlareScoreboard\HapiClient;
use FlareScoreboard\HapiIterator;

final class HapiIteratorTest extends TestCase
{
    public function testGetParameters(): void
    {
        $client = new HapiClient("https://iswa.gsfc.nasa.gov/IswaSystemWebApp/flarescoreboard/hapi/");
        $response = $client->data("SIDC_Operator_FULLDISK", new DateTime("2022-01-01T00:00:00Z"), new DateTime("2022-01-03T00:00:00Z"), null);
        $records = new HapiIterator($response);
        $parameters = $records->getParameters();
        $this->assertEquals(8, count($parameters));
        $this->assertEquals("start_window", $parameters[0]);
    }

    public function testIterating(): void {
        $client = new HapiClient("https://iswa.gsfc.nasa.gov/IswaSystemWebApp/flarescoreboard/hapi/");
        $response = $client->data("SIDC_Operator_FULLDISK", new DateTime("2022-01-01T00:00:00Z"), new DateTime("2022-01-03T00:00:00Z"), null);
        $records = new HapiIterator($response);
        $this->assertTrue(is_iterable($records));
        foreach ($records as $record) {
            $this->assertIsString($record["start_window"]);
        }
    }

    public function testArrayAccess(): void {
        $client = new HapiClient("https://iswa.gsfc.nasa.gov/IswaSystemWebApp/flarescoreboard/hapi/");
        $response = $client->data("SIDC_Operator_FULLDISK", new DateTime("2022-01-01T00:00:00Z"), new DateTime("2022-01-03T00:00:00Z"), null);
        $records = new HapiIterator($response);
        $first_record = $records[0];
        $this->assertEquals("2022-01-01T12:30:00.0", $first_record["start_window"]);
        $this->assertEquals("2022-01-02T12:30:00.0", $first_record["end_window"]);
        $this->assertEquals("2022-01-01T12:30:22.0", $first_record["issue_time"]);
    }
}