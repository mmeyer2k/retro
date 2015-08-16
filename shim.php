<?php

/**
 * shim.php
 * 
 * PHP version 5
 * 
 * @category Retro
 * @package  Retro
 * @author   Michael Meyer (mmeyer2k) <m.meyer2k@gmail.com>
 * @license  http://opensource.org/licenses/MIT The MIT License (MIT)
 * @link     https://github.com/mmeyer2k/retro
 */

if (!function_exists('mysql_escape')) {

    /**
     * Mimics mysql_real_escape_string but without the need for
     * a database connection.
     * 
     * @param string $str
     * @return string
     */
    function mysql_escape($str)
    {
        if(is_array($str)) {
            return array_map(__METHOD__, $str); 
        }
        
        if(!empty($str) && is_string($str)) { 
            return str_replace(array('\\', "\0", "\n", "\r", "'", '"', "\x1a"), array('\\\\', '\\0', '\\n', '\\r', "\\'", '\\"', '\\Z'), $str); 
        } 
        
        return $str;
    }

}

/*
 * Function to convert array to csv string
 */
if (!function_exists('str_putcsv')) {

    /**
     * 
     * @param type $input
     * @param type $delimiter
     * @param type $enclosure
     * @param type $escape_char
     * @return type
     */
    function str_putcsv($input, $delimiter = ',', $enclosure = '"', $escape_char = '\\')
    {
        // Open a memory "file" for read/write...
        $fp = fopen('php://temp', 'r+');
        // ... write the $input array to the "file" using fputcsv()...
        foreach ($input as $i) {
            fputcsv($fp, $i, $delimiter, $enclosure, $escape_char);
        }
        // ... rewind the "file" so we can read what we just wrote...
        rewind($fp);
        // ... read the entire line into a variable...
        $data = fread($fp, 1048576);
        // ... close the "file"...
        fclose($fp);
        // ... and return the $data to the caller, with the trailing newline from fgets() removed.
        return rtrim($data, "\n");
    }

}

/**
 * Shim to allow for hex2bin in less than PHP 5.4.
 * 
 * @link https://stackoverflow.com/questions/17963289/hex2bin-is-not-working-when-i-am-trying-to-use-it-to-convert-encoded-data-to-bin
 */
if (!function_exists('hex2bin')) {

    /**
     * Convery hex to binary.
     * 
     * @param string $hexstr Hexidecimal string to convert to binary.
     * 
     * @return string
     */
    function hex2bin($hexstr)
    {
        $n = strlen($hexstr);
        $sbin = '';
        $i = 0;
        while ($i < $n) {
            $a = substr($hexstr, $i, 2);
            $c = pack('H*', $a);
            if ($i == 0) {
                $sbin = $c;
            } else {
                $sbin.= $c;
            }
            $i+=2;
        }
        return $sbin;
    }

}

/**
 * This file is part of the array_column library
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @copyright Copyright (c) 2013 Ben Ramsey <http://benramsey.com>
 * @license http://opensource.org/licenses/MIT MIT
 */
if (!function_exists('array_column')) {

    /**
     * Returns the values from a single column of the input array, identified by
     * the $columnKey.
     *
     * Optionally, you may provide an $indexKey to index the values in the returned
     * array by the values from the $indexKey column in the input array.
     *
     * @param array $input     A multi-dimensional array (record set) from which 
     *                         to pull a column of values.
     * @param mixed $columnKey The column of values to return. This value may 
     *                         be the integer key of the column you wish to 
     *                         retrieve, or it may be the string key name for an 
     *                         associative array.
     * @param mixed $indexKey  (Optional.) The column to use as the index/keys for
     *                         the returned array. This value may be the integer key
     *                         of the column, or it may be the string key name.
     * 
     * @return array
     */
    function array_column($input = null, $columnKey = null, $indexKey = null)
    {
        // Using func_get_args() in order to check for proper number of
        // parameters and trigger errors exactly as the built-in array_column()
        // does in PHP 5.5.
        $argc = func_num_args();
        $params = func_get_args();

        if ($argc < 2) {
            trigger_error("array_column() expects at least 2 parameters, {$argc} given", E_USER_WARNING);
            return null;
        }

        if (!is_array($params[0])) {
            trigger_error('array_column() expects parameter 1 to be array, ' . gettype($params[0]) . ' given', E_USER_WARNING);
            return null;
        }

        if (!is_int($params[1]) && !is_float($params[1]) && !is_string($params[1]) && $params[1] !== null && !(is_object($params[1]) && method_exists($params[1], '__toString'))
        ) {
            trigger_error('array_column(): The column key should be either a string or an integer', E_USER_WARNING);
            return false;
        }

        if (isset($params[2]) && !is_int($params[2]) && !is_float($params[2]) && !is_string($params[2]) && !(is_object($params[2]) && method_exists($params[2], '__toString'))
        ) {
            trigger_error('array_column(): The index key should be either a string or an integer', E_USER_WARNING);
            return false;
        }

        $paramsInput = $params[0];
        $paramsColumnKey = ($params[1] !== null) ? (string) $params[1] : null;

        $paramsIndexKey = null;
        if (isset($params[2])) {
            if (is_float($params[2]) || is_int($params[2])) {
                $paramsIndexKey = (int) $params[2];
            } else {
                $paramsIndexKey = (string) $params[2];
            }
        }

        $resultArray = array();

        foreach ($paramsInput as $row) {

            $key = $value = null;
            $keySet = $valueSet = false;

            if ($paramsIndexKey !== null && array_key_exists($paramsIndexKey, $row)) {
                $keySet = true;
                $key = (string) $row[$paramsIndexKey];
            }

            if ($paramsColumnKey === null) {
                $valueSet = true;
                $value = $row;
            } elseif (is_array($row) && array_key_exists($paramsColumnKey, $row)) {
                $valueSet = true;
                $value = $row[$paramsColumnKey];
            }

            if ($valueSet) {
                if ($keySet) {
                    $resultArray[$key] = $value;
                } else {
                    $resultArray[] = $value;
                }
            }
        }

        return $resultArray;
    }

}

if (!function_exists('xml2object')) {

    /**
     * 
     * @param type $xml
     * @return type
     */
    function xml2object($xml)
    {
        return simplexml_load_string($xml);
    }

}

if (!function_exists('xml2array')) {

    /**
     * 
     * @param type $xml
     * @return type
     */
    function xml2array($xml)
    {
        return obj2arr(xml2object($xml));
    }

}

if (!function_exists('arr2obj')) {
    function arr2obj($arr) {
        return json_decode(json_encode($arr), false);
    }
}

if (!function_exists('base32_encode')) {

    /**
     * Encode a string in base 32
     * 
     * @param string $str
     * @return string
     */
    function base32_encode($str)
    {
        $_b32table = '1bcd2fgh3jklmn4pqrstavwxyz567890';
        $n = strlen($str) * 8 / 5;
        $arr = str_split($str, 1);
        $m = '';
        foreach ($arr as $c) {
            $m .= str_pad(decbin(ord($c)), 8, '0', STR_PAD_LEFT);
        }
        $p = ceil(strlen($m) / 5) * 5;
        $m = str_pad($m, $p, '0', STR_PAD_RIGHT);
        $newstr = '';
        for ($i = 0; $i < $n; $i++) {
            $newstr .= $_b32table[bindec(substr($m, $i * 5, 5))];
        }

        return $newstr;
    }

}

if (!function_exists('base32_decode')) {

    /**
     * Decode a base32 encoded string
     * 
     * @param string $str
     * @return string
     */
    function base32_decode($str)
    {
        $str = strtolower($str);
        $_b32table = '1bcd2fgh3jklmn4pqrstavwxyz567890';
        $n = strlen($str) * 5 / 8;
        $arr = str_split($str, 1);
        $m = '';
        $split = str_split($_b32table);
        foreach ($arr as $c) {
            $m .= str_pad(decbin(array_search($c, $split)), 5, '0', STR_PAD_LEFT);
        }
        $oldstr = '';
        $floor = floor($n);
        for ($i = 0; $i < $floor; $i++) {
            $oldstr .= chr(bindec(substr($m, $i * 8, 8)));
        }

        return $oldstr;
    }

}

if (!function_exists('obj2arr')) {
    /**
     * Convert object to array.
     *
     * @param object|array $obj Object to convert to array.
     *
     * @return array
     */
    function obj2arr($obj)
    {
        if (is_array($obj)) {
            foreach ($obj as $i => $o) {
                $obj[$i] = obj2arr($o);
            }
            return $obj;
        } elseif (is_object($obj)) {
            return json_decode(json_encode($obj), true);
        }
        return $obj;
    }
}

if (!function_exists('mysqltime')) {
    /**
     * Create a mysql DATETIME string.
     *
     * @param int|null $timestamp PHP timestamp to convert to MySQL DATETIME format
     *
     * @return array
     */
    function mysqltime($timestamp = null)
    {
        if ($timestamp === null) {
            $timestamp = time();
        }
        
        return date('Y-m-d H:i:s', $timestamp);
    }
}

