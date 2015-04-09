<?php

namespace RegionCartographer;

/**
 *
 */
class CityTile
{
    protected $imageHandle;
    protected $absoluteX;
    protected $absoluteY;
    protected $width;
    protected $height;

    /**
     *
     * @param resource $imageHandle
     * @param int $absoluteX
     * @param int $absoluteY
     * @param int $width
     * @param int $height
     */
    function __construct($imageHandle, $absoluteX, $absoluteY, $width, $height)
    {
        $this->imageHandle = $imageHandle;
        $this->absoluteX = $absoluteX;
        $this->absoluteY = $absoluteY;
        $this->width = $width;
        $this->height = $height;
    }

    /**
     *
     * @return resource
     */
    public function getImageHandle()
    {
        return $this->imageHandle;
    }

    /**
     *
     * @return int
     */
    public function getAbsoluteX()
    {
        return $this->absoluteX;
    }

    /**
     *
     * @return int
     */
    public function getAbsoluteY()
    {
        return $this->absoluteY;
    }

    /**
     *
     * @return int
     */
    public function getWidth()
    {
        return $this->width;
    }

    /**
     *
     * @return int
     */
    public function getHeight()
    {
        return $this->height;
    }
}
