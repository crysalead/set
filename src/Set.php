<?php
namespace set;

use Exception;
use BadFunctionCallException;

class Set
{
    /**
     * Merging recursively arrays.
     *
     * Override values for strings identical (unlike `array_merge_recursive()`).
     *
     * @param  array ... list of array to merge.
     * @return array     The merged array.
     */
    public static function merge()
    {
        if (func_num_args() < 2) {
            throw new BadFunctionCallException("Not enough parameters");
        }
        $args = func_get_args();
        $merged = array_shift($args);

        foreach ($args as $source) {
            foreach ($source as $key => $value) {
                if (is_array($value) && isset($merged[$key]) && is_array($merged[$key])) {
                    $merged[$key] = static::merge($merged[$key], $value);
                } elseif (is_int($key)) {
                    $merged[] = $value;
                } else {
                    $merged[$key] = $value;
                }
            }
        }
        return $merged;
    }

    /**
     * Slices an array into two, separating them determined by an array of keys.
     *
     * Usage examples:
     *
     * @param  array        $subject Array that gets split apart.
     * @param  array|string $keys    An array of keys or a single key as string.
     * @return array                 An array containing both arrays, having the array with requested
     *                               keys first and the remainder as second element.
     */
    public static function slice($data, $keys)
    {
        $removed = array_intersect_key($data, array_fill_keys((array) $keys, true));
        $data = array_diff_key($data, $removed);
        return [$data, $removed];
    }

    /**
     * Collapses a multi-dimensional array into a single dimension, using a delimited array path
     * for each array element's key, i.e. [['Foo' => ['Bar' => 'Far']]] becomes
     * ['0.Foo.Bar' => 'Far'].
     *
     * @param  array $data    Array to flatten.
     * @param  array $options Available options are:
     *                        - `'separator'`: String to separate array keys in path (defaults to `'.'`).
     *                        - `'path'`: Starting point (defaults to null).
     * @return array
     */
    public static function flatten($data, $options = [])
    {
        $defaults = ['separator' => '.', 'path' => null];
        $options += $defaults;
        $result = [];

        if (!is_null($options['path'])) {
            $options['path'] .= $options['separator'];
        }
        foreach ($data as $key => $val) {
            if (!is_array($val)) {
                $result[$options['path'] . $key] = $val;
                continue;
            }
            $opts = ['separator' => $options['separator'], 'path' => $options['path'] . $key];
            $result += (array) static::flatten($val, $opts);
        }
        return $result;
    }

    /**
     * Accepts a one-dimensional array where the keys are separated by a delimiter.
     *
     * @param  array $data    The one-dimensional array to expand.
     * @param  array $options The options used when expanding the array:
     *                        - `'separator'` _string_: The delimiter to use when separating keys.
     *                        (defaults to `'.'`).
     * @return array          Returns a multi-dimensional array expanded from a one dimensional
     *                        dot-separated array.
     */
    public static function expand($data, $options =[])
    {
        $defaults = ['separator' => '.'];
        $options += $defaults;
        $result = array();

        foreach ($data as $key => $val) {
            if (strpos($key, $options['separator']) === false) {
                if (!isset($result[$key])) {
                    $result[$key] = $val;
                }
                continue;
            }
            list($path, $key) = explode($options['separator'], $key, 2);
            $path = is_numeric($path) ? intval($path) : $path;
            $result[$path][$key] = $val;
        }
        foreach ($result as $key => $value) {
            if (is_array($value)) {
                $result[$key] = static::expand($value, $options);
            }
        }
        return $result;
    }

    /**
     * Normalizes an array, and converts it to an array of `'key' => 'value'` pairs
     * where keys must be strings.
     *
     * @param  array $data  The array to normalize.
     * @return array        The normalized array.
     */
    public static function normalize($data)
    {
        $result = [];

        foreach ($data as $key => $value) {
            if (!is_int($key)) {
                $result[$key] = $value;
                continue;
            }
            if (!is_scalar($value)) {
                throw new Exception("Invalid array format, a value can't be normalized");
            }
            $result[$value] = null;
        }
        return $result;
    }

}
