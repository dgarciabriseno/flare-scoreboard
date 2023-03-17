<?php declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use FlareScoreboard\Scoreboard;

final class ScoreboardTest extends TestCase
{
    public function testGetRegionPredictors(): void {
        $scoreboard = new Scoreboard();
        $predictors = $scoreboard->getRegionPredictionMethods();
        foreach ($predictors as $predictor) {
            $this->assertStringContainsString("REGIONS", $predictor['id']);
        }
    }

    public function testGetPredictions(): void {
        $scoreboard = new Scoreboard();
        $predictions = $scoreboard->getPredictions("SIDC_Operator_REGIONS", new DateTime("2022-01-01T00:00:00Z"), new DateTime("2022-01-01T23:59:59Z"));
        $this->assertArrayHasKey("data", $predictions);
        $this->assertArrayHasKey("parameters", $predictions);
    }

    public function testBadPredictionMethod(): void {
        $this->expectException(Exception::class);
        $scoreboard = new Scoreboard();
        $scoreboard->getPredictions("SIDC_Operator_REGIONS_BAD", new DateTime("2022-01-01T00:00:00Z"), new DateTime("2022-01-01T23:59:59Z"));
    }
}