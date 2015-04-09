<?php

namespace RegionCartographer;

/**
 *
 */
class CityFactory
{
    const TYPE_MAYOR_VIEW_CITY_DATA = 'ca027edb';
    const TYPE_PNG_FILE = '8a2482b9';
    const INST_MAIN = '00000000';

    protected $dbpfParser;
    protected $metadataEntry;
    protected $tileEntry;

    /**
     *
     * @param DbpfParser $dbpfParser
     * @throws \RuntimeException
     */
    function __construct(DbpfParser $dbpfParser)
    {
        $this->dbpfParser = $dbpfParser;
        $indexList = $this->dbpfParser->getIndexList();

        foreach ($indexList as $indexEntry) {
            $typeId = $indexEntry->getTypeId();
            $instanceId = $indexEntry->getInstanceId();

            if ($typeId == self::TYPE_MAYOR_VIEW_CITY_DATA) {
                $this->metadataEntry = $indexEntry;
            } elseif ($typeId == self::TYPE_PNG_FILE && $instanceId == self::INST_MAIN) {
                $this->tileEntry = $indexEntry;
            }
        }

        if (empty($this->metadataEntry) || empty($this->tileEntry)) {
            throw new \RuntimeException();
        }
    }

    /**
     *
     * @return CityMetadata
     * @throws \RuntimeException
     */
    public function fetchCityMetadata()
    {
        $dataStream = $this->dbpfParser->fetchDataStream($this->metadataEntry, true);

        $versions = [
            9 => 0,
            10 => 4,
            13 => 5
        ];

        $majorVersion = $dataStream->readUnsignedShort();
        $minorVersion = $dataStream->readUnsignedShort();

        if ($majorVersion != 1 || !in_array($minorVersion, array_keys($versions))) {
            throw \RuntimeException();
        }

        $posX = $dataStream->readUnsignedLong();
        $posY = $dataStream->readUnsignedLong();
        $size = $dataStream->readUnsignedLong();
        $dataStream->fseek(18 + $versions[$minorVersion], SEEK_CUR);
        $guid = $dataStream->readUnsignedLong();
        $dataStream->fseek(21, SEEK_CUR);
        $name = $dataStream->readLengthString();

        return new CityMetadata($posX, $posY, $size, $guid, $name);
    }

    /**
     *
     * @param CityMetadata $cityMetadata
     * @return CityTile
     * @throws \RuntimeException
     */
    public function fetchCityTile(CityMetadata $cityMetadata)
    {
        $dataStream = $this->dbpfParser->fetchDataStream($this->tileEntry);

        $imageData = $dataStream->fread($this->tileEntry->getFilesize());
        $tileImageHandle = imagecreatefromstring($imageData);

        if ($tileImageHandle === false) {
            throw new \RuntimeException();
        }

        $cityPosX = $cityMetadata->getPosX();
        $cityPosY = $cityMetadata->getPosY();
        $citySize = $cityMetadata->getSize();

        $tileWidth = imagesx($tileImageHandle);
        $tileHeight = imagesy($tileImageHandle);
        $tileAbsoluteX = (128 * $cityPosX) - (38 * $cityPosX) - (38 * $cityPosY) - (38 * $citySize);
        $tileAbsoluteY = (64 * $cityPosY) + (19 * $cityPosX) - (19 * $cityPosY) + (64 * $citySize) - $tileHeight;

        return new CityTile($tileImageHandle, $tileAbsoluteX, $tileAbsoluteY, $tileWidth, $tileHeight);
    }
}
