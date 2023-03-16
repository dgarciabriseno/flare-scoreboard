<?php declare(strict_types=1);
use PHPUnit\Framework\TestCase;
use FlareScoreboard\HapiClient;
use FlareScoreboard\Models\Dataset;

final class HapiClientTest extends TestCase
{
    public function testCatalog(): void {
        $client = new HapiClient("https://iswa.gsfc.nasa.gov/IswaSystemWebApp/flarescoreboard/hapi/");
        $catalog = $client->catalog();
        $dataset = array_filter($catalog, function (Dataset $dataset) { return $dataset->id === "SIDC_Operator_FULLDISK"; });
        $this->assertEquals(1, count($dataset));
    }
}