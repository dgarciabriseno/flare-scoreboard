<?php declare(strict_types=1);

const DATE_FORMAT = 'Y-m-d\TG:i:s';

include __DIR__ . '/../vendor/autoload.php';
use FlareScoreboard\Scoreboard;
use FlareScoreboard\Json;

/**
 * Return predictions using GET request parameters.
 */
function getPredictions(): array {
    $predictionMethod = $_GET['predictionMethod'] ?? 'SIDC_Operator_REGIONS';
    $start = $_GET['start'] ?? '2022-01-01T00:00:00';
    $stop = $_GET['stop'] ?? '2022-01-01T23:59:59';
    $scoreboard = new Scoreboard();
    return $scoreboard->getPredictions($predictionMethod, new DateTime($start), new DateTime($stop));
}

header('Content-Type: application/json');
echo Json::encode(getPredictions());