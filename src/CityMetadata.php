<?php

namespace RegionCartographer;

/**
 *
 */
class CityMetadata
{
    const SIZE_BIG = 4;
    const SIZE_MEDIUM = 2;
    const SIZE_SMALL = 1;

    protected $posX;
    protected $posY;
    protected $size;
    protected $guid;
    protected $name;

    /**
     *
     * @param int $posX
     * @param int $posY
     * @param int $size
     * @param int $guid
     * @param string $name
     */
    function __construct($posX, $posY, $size, $guid, $name)
    {
        $this->posX = $posX;
        $this->posY = $posY;
        $this->size = $size;
        $this->guid = $guid;
        $this->name = $name;
    }

    /**
     *
     * @return int
     */
    public function getPosX()
    {
        return $this->posX;
    }

    /**
     *
     * @return int
     */
    public function getPosY()
    {
        return $this->posY;
    }

    /**
     *
     * @return int
     */
    public function getSize()
    {
        return $this->size;
    }

    /**
     *
     * @return int
     */
    public function getGuid()
    {
        return $this->guid;
    }

    /**
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }
}
