<?php

/**
 * @date 2017/6/8 17:50:44
 */

namespace Tian\Base;

class Arr
{
    /**
     * 把数组看成一颗树，KEY为路径,$delimiter为路径分隔符,路径不存在会生成
     * $midator可以是整形,字符串,CALLBACK($arr, $key, $delimiter):int|string
     *
     * @param array $arr
     * @param string $key
     * @param string $delimiter
     * @param mixd $midator
     * @return 路径指向的引用
     */
    public static function &ref(array &$arr, $key, $delimiter = '.', $midator = null)
    {
        if (is_null($key))
            return $arr;
        $tmp = &$arr;
        $config = explode($delimiter, trim($key, $delimiter));
        foreach (( array )$config as $d) {
            if (is_callable($midator)) {
                $midator = call_user_func_array($midator, [$arr, $key, $delimiter]);
            }
            if (is_int($midator) || is_string($midator)) {
                if (!isset ($tmp[$midator])) {
                    $tmp [$midator] = [];
                }
                if (!isset ($tmp[$midator] [$d])) {
                    $tmp [$midator][$d] = [];
                }
                $tmp = &$tmp[$midator] [$d];
            } else {
                if (!isset ($tmp [$d])) {
                    $tmp [$d] = [];
                }
                $tmp = &$tmp [$d];
            }
        }
        return $tmp;
    }

    /**
     * 把数组看成一颗树，KEY为路径,$delimiter为路径分隔符,路径不存在会生成
     * $midator可以是整形,字符串,CALLBACK($arr, $key, $delimiter):int|string
     *
     * @param array $arr
     * @param string $key
     * @param string $name
     * @param string $delimiter
     * @param mixd $midator
     * @return $arr
     */
    public
    static function set(array &$arr, $key, $name, $delimiter = '.', $midator = null)
    {
        if (is_null($key)) {
            $arr = $name;
        } else {
            $tmp = &self::ref($arr, $key, $delimiter, $midator);
            if (is_callable($midator)) {
                $midator = call_user_func_array($midator, [$arr, $key, $delimiter]);
            }
            if (is_int($midator) || is_string($midator)) {
                $tmp[] = $name;
            }
            $tmp = $name;
        }

        return $arr;
    }

    /**
     * 把数组看成一颗树，KEY为路径,$delimiter为路径分隔符,路径不存在返回FALSE
     * $midator可以是整形,字符串,CALLBACK($arr, $key, $delimiter):int|string
     * @param array $arr
     * @param string $key
     * @param string $delimiter
     * @return bool
     */
    public
    static function has(array $arr, $key, $delimiter = '.', $midator = null)
    {
        $tmp = $arr;
        $config = explode($delimiter, trim($key, $delimiter));
        foreach (( array )$config as $d) {
            if (is_callable($midator)) {
                $midator = call_user_func_array($midator, [$arr, $key, $delimiter]);
            }
            if (is_int($midator) || is_string($midator)) {
                if (!isset ($tmp[$midator])) {
                    return false;
                }
                if (!isset ($tmp[$midator] [$d])) {
                    return false;
                }
                $tmp = $tmp[$midator] [$d];
            } else {
                if (isset ($tmp [$d])) {
                    $tmp = $tmp [$d];
                } else {
                    return false;
                }
            }

        }
        return true;
    }

    /**
     * 把数组看成一颗树，KEY为路径,$delimiter为路径分隔符,路径不存在返回默认值
     * $midator可以是整形,字符串,CALLBACK($arr, $key, $delimiter):int|string
     * @param array $arr
     * @param string $key
     * @param string $default
     * @param string $delimiter
     * @return string|unknown
     */
    public
    static function get(array $arr, $key, $default = null, $delimiter = '.', $midator = null)
    {
        $tmp = $arr;
        $config = explode($delimiter, trim($key, $delimiter));
        if (is_callable($midator)) {
            $midator = call_user_func_array($midator, [$arr, $key, $delimiter]);
        }
        foreach (( array )$config as $d) {
            if ((is_int($midator) || is_string($midator))) {
                if (isset($tmp[$midator] [$d]))
                    $tmp = $tmp[$midator] [$d];
                else
                    return $default;
            } else {
                if (isset ($tmp [$d])) {
                    $tmp = $tmp [$d];
                } else {
                    return $default;
                }
            }

        }
        return $tmp;
    }

    /**
     * Determine if the given key exists in the provided array.
     *
     * @param \ArrayAccess|array $array
     * @param string|int $key
     * @return bool
     */
    public
    static function exists($array, $key)
    {
        if ($array instanceof ArrayAccess) {
            return $array->offsetExists($key);
        }
        return array_key_exists($key, $array);
    }

    /**
     * Get all of the given array except for a specified array of items.
     *
     * @param array $array
     * @param array|string $keys
     * @return array
     */
    public
    static function except($array, $keys)
    {
        static::forget($array, $keys);
        return $array;
    }

    /**
     * Flatten a multi-dimensional associative array with dots.
     *
     * @param array $array
     * @param string $prepend
     * @return array
     */
    public
    static function dot($array, $prepend = '', $delimiter = '.')
    {
        $results = [];
        foreach ($array as $key => $value) {
            if (is_array($value) && !empty ($value)) {
                $results = array_merge($results, static::dot($value, $prepend . $key . $delimiter));
            } else {
                $results [$prepend . $key] = $value;
            }
        }
        return $results;
    }

    /**
     * Remove one or many array items from a given array using "dot" notation.
     *
     * @param array $array
     * @param array|string $keys
     * @return void
     */
    public
    static function forget(&$array, $keys, $delimiter = '.')
    {
        $original = &$array;
        $keys = ( array )$keys;
        if (count($keys) === 0) {
            return;
        }
        foreach ($keys as $key) {
            // if the exact key exists in the top-level, remove it
            if (static::exists($array, $key)) {
                unset ($array [$key]);
                continue;
            }
            $parts = explode($delimiter, trim($key, $delimiter));
            // clean up before each pass
            $array = &$original;
            while (count($parts) > 1) {
                $part = array_shift($parts);
                if (isset ($array [$part]) && is_array($array [$part])) {
                    $array = &$array [$part];
                } else {
                    continue 2;
                }
            }
            unset ($array [array_shift($parts)]);
        }
    }

    /**
     * Shuffle the given array and return the result.
     *
     * @param array $array
     * @return array
     */
    public
    static function shuffle($array)
    {
        shuffle($array);
        return $array;
    }

    /**
     * Determines if an array is associative.
     *
     * An array is "associative" if it doesn't have sequential numerical keys beginning with zero.
     *
     * @param array $array
     * @return bool
     */
    public
    static function isAssoc(array $array)
    {
        $keys = array_keys($array);
        return array_keys($keys) !== $keys;
    }

    /**
     * https://travis-ci.org/ramsey/array_column
     * Returns the values from a single column of the input array, identified by
     * the $columnKey.
     *
     * Optionally, you may provide an $indexKey to index the values in the returned
     * array by the values from the $indexKey column in the input array.
     *
     * @param array $input
     *            A multi-dimensional array (record set) from which to pull
     *            a column of values.
     * @param mixed $columnKey
     *            The column of values to return. This value may be the
     *            integer key of the column you wish to retrieve, or it
     *            may be the string key name for an associative array.
     * @param mixed $indexKey
     *            (Optional.) The column to use as the index/keys for
     *            the returned array. This value may be the integer key
     *            of the column, or it may be the string key name.
     * @return array
     */
    public
    static function column($input = null, $columnKey = null, $indexKey = null)
    {
        if (function_exists('array_column')) {
            return array_column($input, $columnKey, $indexKey);
        } else {

            // Using func_get_args() in order to check for proper number of
            // parameters and trigger errors exactly as the built-in array_column()
            // does in PHP 5.5.
            $argc = func_num_args();
            $params = func_get_args();

            if ($argc < 2) {
                trigger_error("array_column() expects at least 2 parameters, {$argc} given", E_USER_WARNING);
                return null;
            }

            if (!is_array($params [0])) {
                trigger_error('array_column() expects parameter 1 to be array, ' . gettype($params [0]) . ' given', E_USER_WARNING);
                return null;
            }

            if (!is_int($params [1]) && !is_float($params [1]) && !is_string($params [1]) && $params [1] !== null && !(is_object($params [1]) && method_exists($params [1], '__toString'))) {
                trigger_error('array_column(): The column key should be either a string or an integer', E_USER_WARNING);
                return false;
            }

            if (isset ($params [2]) && !is_int($params [2]) && !is_float($params [2]) && !is_string($params [2]) && !(is_object($params [2]) && method_exists($params [2], '__toString'))) {
                trigger_error('array_column(): The index key should be either a string or an integer', E_USER_WARNING);
                return false;
            }

            $paramsInput = $params [0];
            $paramsColumnKey = ($params [1] !== null) ? ( string )$params [1] : null;

            $paramsIndexKey = null;
            if (isset ($params [2])) {
                if (is_float($params [2]) || is_int($params [2])) {
                    $paramsIndexKey = ( int )$params [2];
                } else {
                    $paramsIndexKey = ( string )$params [2];
                }
            }

            $resultArray = array();

            foreach ($paramsInput as $row) {
                $key = $value = null;
                $keySet = $valueSet = false;

                if ($paramsIndexKey !== null && array_key_exists($paramsIndexKey, $row)) {
                    $keySet = true;
                    $key = ( string )$row [$paramsIndexKey];
                }

                if ($paramsColumnKey === null) {
                    $valueSet = true;
                    $value = $row;
                } elseif (is_array($row) && array_key_exists($paramsColumnKey, $row)) {
                    $valueSet = true;
                    $value = $row [$paramsColumnKey];
                }

                if ($valueSet) {
                    if ($keySet) {
                        $resultArray [$key] = $value;
                    } else {
                        $resultArray [] = $value;
                    }
                }
            }

            return $resultArray;
        }
    }

    /**
     * Filter the array using the given callback.
     * pass both value and key as arguments to callback instead of the value
     *
     * @param array $array
     * @param callable $callback ($value,$key)
     * @return array
     */
    public
    static function where($array, callable $callback)
    {
        return array_filter($array, $callback, ARRAY_FILTER_USE_BOTH);
    }
}