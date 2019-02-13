<?php
/*
 * This file is part of ehaomiao/slim.
 *
 * (c) Haomiao Inc. <dev@ehaomiao.com>
 *
 * For the full copyright and license information, please view the "LICENSE.md"
 * file that was distributed with this source code.
 */

if (!function_exists('i18n')) {
    /**
     * 多语言.
     *
     * @return string
     */
    function i18n()
    {
        return call_user_func_array('sprintf', func_get_args());
    }
}

if (!function_exists('snake')) {
    /**
     * Convert a string to snake case.
     *
     * @param  string  $value
     * @param  string  $delimiter
     * @return string
     */
    function snake($value, $delimiter = '_')
    {
        $value = trim(preg_replace_callback(
            '#[A-Z]#',
            function ($matches) use ($delimiter) {
                return $delimiter . strtolower($matches[0]);
            },
            $value
        ), $delimiter);

        return $value;
    }

}

if (!function_exists('camel')) {
    /**
     * Convert a value to camel case.
     *
     * @param  string  $value
     * @return string
     */
    function camel($value)
    {
        return lcfirst(studly($value));
    }
}

if (!function_exists('studly')) {
    /**
     * Convert a value to studly caps case.
     *
     * @param  string  $value
     * @return string
     */
    function studly($value)
    {
        $value = ucwords(str_replace(['-', '_'], ' ', strtolower($value)));
        return str_replace(' ', '', $value);
    }
}