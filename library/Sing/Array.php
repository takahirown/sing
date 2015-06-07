<?php

/**
 * @category  Sing
 * @author    t.watanabe
 * @since     2015/05/09
 */
class Sing_Array
{
    public static function between($start, $end)
    {
        $result = array();
        for ($idx = $start; $idx <= $end; $idx++) {
            $result[$idx] = $idx;
        }
        return $result;
    }

    public static function dumpJson($array)
    {
        return json_encode($array, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
    }
}