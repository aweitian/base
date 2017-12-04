<?php

/**
 * @date 2017/6/8 17:50:44
 */

namespace Tian\Base;

use ArrayAccess;
use Closure;
use InvalidArgumentException;

class Arr
{
    /**
     * 把数组看成一颗树，KEY为路径,$delimiter为路径分隔符,路径不存在会生成
     * $midator可以是整形,字符串,CALLBACK($arr, $key, $delimiter):int|string
     *
     * @param array $arr
     * @param string $key
     * @param string $delimiter
     * @param $midator
     * @return array 路径指向的引用
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
     * @param $midator
     * @return array
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
     * @param null $midator
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
     * @param null $midator
     * @return string
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
     * Return the first element in an array passing a given truth test.
     *
     * @param  array  $array
     * @param  callable|null  $callback
     * @param  mixed  $default
     * @return mixed
     */
    public static function first($array, callable $callback = null, $default = null)
    {
        if (is_null($callback)) {
            if (empty($array)) {
                return self::value($default);
            }

            foreach ($array as $item) {
                return $item;
            }
        }

        foreach ($array as $key => $value) {
            if (call_user_func($callback, $value, $key)) {
                return $value;
            }
        }

        return self::value($default);
    }

    /**
     * Return the last element in an array passing a given truth test.
     *
     * @param  array  $array
     * @param  callable|null  $callback
     * @param  mixed  $default
     * @return mixed
     */
    public static function last($array, callable $callback = null, $default = null)
    {
        if (is_null($callback)) {
            return empty($array) ? self::value($default) : end($array);
        }

        return static::first(array_reverse($array, true), $callback, $default);
    }


    /**
     * Flatten a multi-dimensional array into a single level.
     *
     * @param  array  $array
     * @param  int  $depth
     * @return array
     */
    public static function flatten($array, $depth = INF)
    {
        return array_reduce($array, function ($result, $item) use ($depth) {
            $item = $item instanceof Collection ? $item->all() : $item;

            if (! is_array($item)) {
                return array_merge($result, [$item]);
            } elseif ($depth === 1) {
                return array_merge($result, array_values($item));
            } else {
                return array_merge($result, static::flatten($item, $depth - 1));
            }
        }, []);
    }

    /**
     * Get a subset of the items from the given array.
     *
     * @param  array  $array
     * @param  array|string  $keys
     * @return array
     */
    public static function only($array, $keys)
    {
        return array_intersect_key($array, array_flip((array) $keys));
    }

    /**
     * Explode the "value" and "key" arguments passed to "pluck".
     *
     * @param  string|array  $value
     * @param  string|array|null  $key
     * @return array
     */
    protected static function explodePluckParameters($value, $key)
    {
        $value = is_string($value) ? explode('.', $value) : $value;

        $key = is_null($key) || is_array($key) ? $key : explode('.', $key);

        return [$value, $key];
    }
    /**
     * Pluck an array of values from an array.
     *
     * @param  array  $array
     * @param  string|array  $value
     * @param  string|array|null  $key
     * @return array
     */
    public static function pluck($array, $value, $key = null)
    {
        $results = [];

        list($value, $key) = static::explodePluckParameters($value, $key);

        foreach ($array as $item) {
            $itemValue = data_get($item, $value);

            // If the key is "null", we will just append the value to the array and keep
            // looping. Otherwise we will key the array using the value of the key we
            // received from the developer. Then we'll return the final array form.
            if (is_null($key)) {
                $results[] = $itemValue;
            } else {
                $itemKey = data_get($item, $key);

                if (is_object($itemKey) && method_exists($itemKey, '__toString')) {
                    $itemKey = (string) $itemKey;
                }

                $results[$itemKey] = $itemValue;
            }
        }

        return $results;
    }


    /**
     * Push an item onto the beginning of an array.
     *
     * @param  array  $array
     * @param  mixed  $value
     * @param  mixed  $key
     * @return array
     */
    public static function prepend($array, $value, $key = null)
    {
        if (is_null($key)) {
            array_unshift($array, $value);
        } else {
            $array = [$key => $value] + $array;
        }

        return $array;
    }

    /**
     * Get a value from the array, and remove it.
     *
     * @param  array   $array
     * @param  string  $key
     * @param  mixed   $default
     * @return mixed
     */
    public static function pull(&$array, $key, $default = null)
    {
        $value = static::get($array, $key, $default);

        static::forget($array, $key);

        return $value;
    }

    /**
     * Get one or a specified number of random values from an array.
     *
     * @param  array  $array
     * @param  int|null  $number
     * @return mixed
     *
     * @throws \InvalidArgumentException
     */
    public static function random($array, $number = null)
    {
        $requested = is_null($number) ? 1 : $number;

        $count = count($array);

        if ($requested > $count) {
            throw new InvalidArgumentException(
                "You requested {$requested} items, but there are only {$count} items available."
            );
        }

        if (is_null($number)) {
            return $array[array_rand($array)];
        }

        if ((int) $number === 0) {
            return [];
        }

        $keys = array_rand($array, $number);

        $results = [];

        foreach ((array) $keys as $key) {
            $results[] = $array[$key];
        }

        return $results;
    }

    /***
     * @param $value
     * @return mixed
     */
    public static function value($value)
    {
        return $value instanceof Closure ? $value() : $value;
    }
    /**
     * Flatten a multi-dimensional associative array with dots.
     *
     * @param array $array
     * @param string $prepend
     * @param string $delimiter
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
     * Recursively sort an array by keys and values.
     *
     * @param  array  $array
     * @return array
     */
    public static function sortRecursive($array)
    {
        foreach ($array as &$value) {
            if (is_array($value)) {
                $value = static::sortRecursive($value);
            }
        }

        if (static::isAssoc($array)) {
            ksort($array);
        } else {
            sort($array);
        }

        return $array;
    }
    /**
     * Remove one or many array items from a given array using "dot" notation.
     *
     * @param array $array
     * @param array|string $keys
     * @param string $delimiter
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
     * Determine whether the given value is array accessible.
     *
     * @param  mixed  $value
     * @return bool
     */
    public static function accessible($value)
    {
        return is_array($value) || $value instanceof ArrayAccess;
    }
    /**
     * Collapse an array of arrays into a single array.
     *
     * @param  array  $array
     * @return array
     */
    public static function collapse($array)
    {
        $results = [];

        foreach ($array as $values) {
            if ($values instanceof Collection) {
                $values = $values->all();
            } elseif (! is_array($values)) {
                continue;
            }

            $results = array_merge($results, $values);
        }

        return $results;
    }


    /**
     * Divide an array into two arrays. One with keys and the other with values.
     *
     * @param  array  $array
     * @return array
     */
    public static function divide($array)
    {
        return [array_keys($array), array_values($array)];
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
                return null;
            }

            if (isset ($params [2]) && !is_int($params [2]) && !is_float($params [2]) && !is_string($params [2]) && !(is_object($params [2]) && method_exists($params [2], '__toString'))) {
                trigger_error('array_column(): The index key should be either a string or an integer', E_USER_WARNING);
                return null;
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


    /**
     * Sort the array using the given callback or "dot" notation.
     *
     * @param  array  $array
     * @param  callable|string  $callback
     * @return array
     */
    public static function sort($array, $callback)
    {
        return Collection::make($array)->sortBy($callback)->all();
    }

    /**
     * If the given value is not an array, wrap it in one.
     *
     * @param  mixed  $value
     * @return array
     */
    public static function wrap($value)
    {
        return ! is_array($value) ? [$value] : $value;
    }
}