<?php

namespace common\components;

class Helper
{
    /**
     * Check datetime format
     *
     * @param string $date
     * @param string $format
     * @return bool
     */
    public static function validateDate($date, $format = 'Y-m-d')
    {
        $d = \DateTime::createFromFormat($format, $date);
        return $d && $d->format($format) == $date;
    }

    /**
     * Shuffle data
     *
     * @param $data
     * @param int $limit
     * @return array
     */
    public static function shuffleData($data, $limit = 2)
    {
        if (!is_array($data) || empty($data)) return [];

        shuffle($data);
        $newData = [];
        $offset = 0;
        foreach ($data as $val) {
            if ($offset >= $limit) break;
            $newData[$offset] = $val;
            $offset++;
        }

        return $newData;
    }

    /**
     * Split string by words
     *
     * @param string $string
     * @param null $length
     * @return string
     */
    public static function wordSplit($string, $length = null)
    {
        if ((int)$length > 0) {
            $length = (int)$length;

            if (function_exists('mb_strlen')) {
                $l = mb_strlen($string, 'utf-8');
            } else {
                $l = iconv_strlen($string, 'utf-8');
            }

            if ($l > $length) {
                if (function_exists('mb_substr')) {
                    $string = mb_substr($string, 0, $length);
                } else {
                    $string = iconv_substr($string, 0, $length);
                }
            }
        }

        return $string;
    }
}
