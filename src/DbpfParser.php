<?php

namespace RegionCartographer;

/**
 *
 */
class DbpfParser
{
    protected $indexList;
    protected $fileStream;

    /*
     * PHP DBPF decompression by Delphy
     * Thanks to dmchess (http://hullabaloo.simshost.com/forum/viewtopic.php?t=6578&postdays=0&postorder=asc)
     */

    /**
     *
     * @param int $len
     * @return string
     */
    protected function decompress($len = 0)
    {
        $buf = '';
        $answer = '';
        $answerlen = 0;
        $numplain = '';
        $numcopy = '';
        $offset = '';

        if ($len == 0) {
            $len = $this->fileStream->readUnsignedLong() - 9;
            $this->fileStream->fseek(5, SEEK_CUR);
        }

        for (; $len > 0;) {
            $cc = $this->fileStream->readUnsignedChar();
            $len -= 1;

            if ($cc >= 252) { // 0xFC
                $numplain = $cc & 0x03;
                if ($numplain > $len) {
                    $numplain = $len;
                }
                $numcopy = 0;
                $offset = 0;
            } elseif ($cc >= 224) { // 0xE0
                $numplain = ($cc - 0xdf) << 2;
                $numcopy = 0;
                $offset = 0;
            } elseif ($cc >= 192) { // 0xC0
                $len -= 3;

                $byte1 = $this->fileStream->readUnsignedChar();
                $byte2 = $this->fileStream->readUnsignedChar();
                $byte3 = $this->fileStream->readUnsignedChar();

                $numplain = $cc & 0x03;
                $numcopy = (($cc & 0x0c) << 6) + 5 + $byte3;
                $offset = (($cc & 0x10) << 12 ) + ($byte1 << 8) + $byte2;
            } elseif ($cc >= 128) { // 0x80
                $len -= 2;

                $byte1 = $this->fileStream->readUnsignedChar();
                $byte2 = $this->fileStream->readUnsignedChar();

                $numplain = ($byte1 & 0xc0) >> 6;
                $numcopy = ($cc & 0x3f) + 4;
                $offset = (($byte1 & 0x3f) << 8) + $byte2;
            } else {
                $len -= 1;

                $byte1 = $this->fileStream->readUnsignedChar();

                $numplain = ($cc & 0x03);
                $numcopy = (($cc & 0x1c) >> 2) + 3;
                $offset = (($cc & 0x60) << 3) + $byte1;
            }

            $len -= $numplain;

            if ($numplain > 0) {
                $buf = $this->fileStream->fread($numplain);
                $answer = $answer . $buf;
            }

            $fromoffset = strlen($answer) - ($offset + 1);
            for ($i = 0; $i < $numcopy; ++$i) {
                $answer = $answer . substr($answer, $fromoffset + $i, 1);
            }

            $answerlen += $numplain;
            $answerlen += $numcopy;
        }

        return $answer;
    }

    /**
     *
     * @param string $filePath
     * @throws \RuntimeException
     */
    public function __construct($filePath)
    {
        $this->indexList = new \SplDoublyLinkedList();
        $this->fileStream = new DataStructReader($filePath, 'rb');
        $this->fileStream->flock(LOCK_SH);

        $identifier = $this->fileStream->fread(4);
        $majorVersion = $this->fileStream->readUnsignedLong();
        $minorVersion = $this->fileStream->readUnsignedLong();
        $this->fileStream->fseek(20, SEEK_CUR);
        $indexMajorVersion = $this->fileStream->readUnsignedLong();

        if ($identifier != 'DBPF' || $majorVersion != 1 || $minorVersion != 0 || $indexMajorVersion != 7) {
            throw new \RuntimeException();
        }

        $indexCount = $this->fileStream->readUnsignedLong();
        $indexOffset = $this->fileStream->readUnsignedLong();
        $this->fileStream->fseek($indexOffset);

        for ($i = 0; $i < $indexCount; $i++) {
            $typeId = DataStructReader::stringToHex($this->fileStream->fread(4), true);
            $this->fileStream->fseek(4, SEEK_CUR);
            $instanceId = DataStructReader::stringToHex($this->fileStream->fread(4), true);
            $offset = $this->fileStream->readUnsignedLong();
            $filesize = $this->fileStream->readUnsignedLong();

            $indexEntry = new IndexEntry($typeId, $instanceId, $offset, $filesize);
            $this->indexList->push($indexEntry);
        }
    }

    /**
     *
     * @param IndexEntry $indexEntry
     * @param boolean $compressed
     * @return DataStructReader
     * @throws \RuntimeException
     */
    public function fetchDataStream(IndexEntry $indexEntry, $compressed = false)
    {
        $this->fileStream->fseek($indexEntry->getOffset());

        if ($compressed) {
            $data = $this->decompress();
        } else {
            $data = $this->fileStream->fread($indexEntry->getFilesize());
        }

        $dataStream = new DataStructReader('php://temp', 'r+b');
        $dataStream->fwrite($data);
        $dataStream->rewind();

        return $dataStream;
    }

    /**
     *
     * @return \SplDoublyLinkedList
     */
    public function getIndexList()
    {
        return $this->indexList;
    }

    /**
     *
     * @return DataStructReader
     */
    public function getFileStream()
    {
        return $this->fileStream;
    }
}
