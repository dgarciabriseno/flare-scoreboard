# Helioviewer Flare Scoreboard
This module is the library used to display data from the Community Coordinated Modeling Center's (CCMC) Flare Scoreboard in Helioviewer.

The [Flare Scoreboard](https://ccmc.gsfc.nasa.gov/scoreboards/flare/) is a system that stores predictions for solar flares.

## Usage
- Checkout this repository
- Run `composer dump-autoload` to generate autoloader for namespaces
- In php `include vendor/autoload.php`
- Use the scoreboard class:
```php
$scoreboard = new Scoreboard();
$scoreboard->getPredictions("SIDC_Operator_REGIONS", new DateTime("2023-01-01T00:00:00"), new DateTime("2023-01-01T23:59:59"));
```