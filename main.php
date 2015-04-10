<?php

require_once 'vendor/autoload.php';

use RegionCartographer\DbpfParser;
use RegionCartographer\CityFactory;


$directory = new DirectoryIterator(@$argv[1]);

foreach ($directory as $entry) {
    if ($entry->isFile() && $entry->isReadable() && $entry->getExtension() == 'sc4') {
        $parser = new DbpfParser($entry->getPathname());
        $factory = new CityFactory($parser);

        $cityMetadata = $factory->fetchCityMetadata();
        var_dump($cityMetadata);

        $cityTile = $factory->fetchCityTile($cityMetadata);
        var_dump($cityTile);

        imagepng($cityTile->getImageHandle(), 'examples/' . $cityMetadata->getGuid() . '.png');
    }
}
