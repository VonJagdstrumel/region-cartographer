<?php

namespace RegionCartographer;

/**
 *
 */
class IndexEntry
{
    protected $typeId;
    protected $instanceId;
    protected $offset;
    protected $filesize;

    /**
     *
     * @param string $typeId
     * @param string $instanceId
     * @param int $offset
     * @param int $filesize
     */
    function __construct($typeId, $instanceId, $offset, $filesize)
    {
        $this->typeId = $typeId;
        $this->instanceId = $instanceId;
        $this->offset = $offset;
        $this->filesize = $filesize;
    }

    /**
     *
     * @return string
     */
    public function getTypeId()
    {
        return $this->typeId;
    }

    /**
     *
     * @return string
     */
    public function getInstanceId()
    {
        return $this->instanceId;
    }

    /**
     *
     * @return int
     */
    public function getOffset()
    {
        return $this->offset;
    }

    /**
     *
     * @return int
     */
    public function getFilesize()
    {
        return $this->filesize;
    }
}
