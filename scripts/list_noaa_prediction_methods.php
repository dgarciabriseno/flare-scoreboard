<?php declare(strict_types=1);

include __DIR__ . '/../vendor/autoload.php';

use FlareScoreboard\HapiClient;

$server = new HapiClient("https://iswa.gsfc.nasa.gov/IswaSystemWebApp/flarescoreboard/hapi");
$catalog = $server->catalog();
foreach ($catalog['catalog'] as $predictor) {
    $id = $predictor['id'];
    if (str_contains($id, "REGIONS")) {
        $info = $server->info($id);
        $parameters = $info['parameters'];
        $noaaParameters = array_filter($parameters, function ($parameter) {
            return str_contains($parameter['name'], "NOAARegionId");
        });
        if (count($noaaParameters) > 0) {
            echo "$id\n";
        } else {
            echo "NOAARegionId not found in $id\n";
        }
    }
}