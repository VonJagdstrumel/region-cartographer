<?php

require_once 'vendor/autoload.php';

use RegionCartographer\DbpfParser;
use RegionCartographer\CityFactory;

$size = 0;

$directory = new DirectoryIterator(@$argv[1]);

foreach ($directory as $entry) {
    if ($entry->isFile() && $entry->isReadable() && $entry->getExtension() == 'sc4') {
        $parser = new DbpfParser($entry->getPathname());
        $factory = new CityFactory($parser);

        $cityMetadata = $factory->fetchCityMetadata();
        $cityTile = $factory->fetchCityTile($cityMetadata);

        $cities[$cityMetadata->getPosX()][$cityMetadata->getPosY()] = [$cityMetadata, $cityTile];

        $size = max([
            $cityMetadata->getPosX(),
            $cityMetadata->getPosY(),
            $size
        ]);
    }
}

$regionImageHandle = imagecreatetruecolor(2000, 2000);
$offsetX = 600;
$offsetY = 500;

for ($i = 0, $diagonal = 1; $diagonal > 0; $diagonal = $size - abs( ++$i - $size) + 1) {
    $min = max($i - $size, 0);
    $max = min($i, $size);

    for ($j = 0; $j <= $max - $min; ++$j) {
        $x = ($max - $j);
        $y = ($j + $min);

        if (isset($cities[$x][$y])) {
            $city = $cities[$x][$y];
            $cityMetadata = $city[0];
            $cityTile = $city[1];

            imagecopy($regionImageHandle, $cityTile->getImageHandle(), $cityTile->getAbsoluteX() + $offsetX, $cityTile->getAbsoluteY() + $offsetY, 0, 0, $cityTile->getWidth(), $cityTile->getHeight());
        }
    }
}

header('Content-type: image/png');
imagepng($regionImageHandle);
imagedestroy($regionImageHandle);
