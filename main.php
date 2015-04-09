<?php

require_once 'vendor/autoload.php';

use RegionCartographer\DbpfParser;
use RegionCartographer\CityFactory;

$parser = new DbpfParser(@$argv[1]);
$factory = new CityFactory($parser);

$cityMetadata = $factory->fetchCityMetadata();
var_dump($cityMetadata);

$cityTile = $factory->fetchCityTile($cityMetadata);
var_dump($cityTile);
