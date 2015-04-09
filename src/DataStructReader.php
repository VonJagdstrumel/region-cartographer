<?php

namespace RegionCartographer;

/**
 *
 */
class DataStructReader extends \SplFileObject
{

    /**
     *
     * @return int
     */
    public function readUnsignedLong()
    {
        $d = $this->fread(4);
        $a = unpack('Vn', $d);
        return $a['n'];
    }

    /**
     *
     * @return int
     */
    public function readUnsignedShort()
    {
        $d = $this->fread(2);
        $a = unpack('vn', $d);
        return $a['n'];
    }

    /**
     *
     * @return int
     */
    public function readUnsignedChar()
    {
        $d = $this->fread(1);
        $a = unpack('Cn', $d);
        return $a['n'];
    }

    /**
     *
     * @param int $len
     * @return string
     */
    public function readString($len)
    {
        return $this->fread($len);
    }

    /**
     *
     * @param int $len
     * @return string
     */
    public function readUnicodeString($len)
    {
        $s = '';
        for ($i = 0; $i < $len; ++$i) {
            $c = $this->fread(1);
            if (ord($c) != 0) {
                $s .= $c;
            }
            $this->fseek(1, SEEK_CUR);
        }
        return $s;
    }

    /**
     *
     * @return string
     */
    public function readNullString()
    {
        $s = '';
        $c = $this->fread(1);
        while (ord($c) != 0) {
            $s .= $c;
            $c = $this->fread(1);
        }
        return $s;
    }

    /**
     *
     * @return string
     */
    public function readLengthString()
    {
        $len = $this->readUnsignedLong();
        $s = $this->readString($len);
        return $s;
    }

    /**
     *
     * @return string
     */
    public function readLengthUnicodeString()
    {
        $len = $this->readUnsignedLong();
        $s = $this->readUnicodeString($len);
        return $s;
    }

    /**
     *
     * @param string $string
     * @param boolean $reverse
     * @return string
     */
    public static function stringToHex($string, $reverse = false)
    {
        $hex = '';
        for ($i = 0; $i < strlen($string); ++$i) {
            $hex .= str_pad(dechex(ord($string[$i])), 2, '0', STR_PAD_LEFT);
        }
        if ($reverse) {
            $hex = self::reverseHex($hex);
        }
        return $hex;
    }

    /**
     *
     * @param string $hex
     * @param boolean $reverse
     * @return string
     */
    public static function hexToString($hex, $reverse = false)
    {
        if ($reverse) {
            $hex = self::reverseHex($hex);
        }
        $string = '';
        for ($i = 0; $i < strlen($hex) - 1; $i += 2) {
            $string .= chr(hexdec($hex[$i] . $hex[$i + 1]));
        }
        return $string;
    }

    /**
     *
     * @param string $string
     * @return string
     */
    public static function reverseHex($string)
    {
        $revstring = '';
        for ($i = 0; $i < strlen($string) - 1; $i += 2) {
            $revstring = $string[$i] . $string[$i + 1] . $revstring;
        }
        return $revstring;
    }
}
