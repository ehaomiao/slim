<?php

/*
 * This file is part of the long/framework package.
 *
 * (c) Sinpe <support@sinpe.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Haomiao\Slim;

/**
 * DataObject
 *
 * @package Sinpe\Framework
 * @since   1.0.0
 */
class DataObject implements DataObjectInterface
{
    /**
     * The data
     *
     * @var array
     */
    protected $data = [];

    /**
     * __construct
     *
     * @param array $items Pre-populate object with this key-value array
     */
    public function __construct(array $data = [])
    {
        $this->data = $data;
    }

    /**
     * Set object item
     *
     * @param string $key   The data key
     * @param mixed  $value The data value
     */
    public function set($key, $value)
    {
        $this->data[$key] = $value;
    }

    /**
     * Get object item for key
     *
     * @param string $key     The data key
     * @param mixed  $default The default value to return if data key does not exist
     *
     * @return mixed The key's value, or the default value
     */
    public function get($key, $default = null)
    {
        return $this->has($key) ? $this->data[$key] : $default;
    }

    // /**
    //  * Add item to object, replacing existing items with the same data key
    //  *
    //  * @param array $items Key-value array of data to append to this object
    //  */
    // public function replace(array $items)
    // {
    //     foreach ($items as $key => $value) {
    //         $this->set($key, $value);
    //     }
    // }

    /**
     * Get all items in object
     *
     * @return array The object's source data
     */
    public function all()
    {
        return $this->data;
    }

    /**
     * Get object keys
     *
     * @return array The object's source data keys
     */
    public function keys()
    {
        return array_keys($this->data);
    }

    /**
     * Does this object have a given key?
     *
     * @param string $key The data key
     *
     * @return bool
     */
    public function has($key)
    {
        return array_key_exists($key, $this->data);
    }

    // /**
    //  * Remove item from object
    //  *
    //  * @param string $key The data key
    //  */
    // public function remove($key)
    // {
    //     unset($this->data[$key]);
    // }

    /**
     * Remove all items from object
     */
    public function reset()
    {
        $this->data = [];
    }

    /**
     * Does this object have a given key?
     *
     * @param  string $key The data key
     *
     * @return bool
     */
    public function offsetExists($key)
    {
        return $this->has($key);
    }

    /**
     * Get object item for key
     *
     * @param string $key The data key
     *
     * @return mixed The key's value, or the default value
     */
    public function offsetGet($key)
    {
        return $this->get($key);
    }

    /**
     * Set object item
     *
     * @param string $key   The data key
     * @param mixed  $value The data value
     */
    public function offsetSet($key, $value)
    {
        $this->set($key, $value);
    }

    /**
     * Remove item from object
     *
     * @param string $key The data key
     */
    public function offsetUnset($key)
    {
        $this->remove($key);
    }

    /**
     * Get number of items in object
     *
     * @return int
     */
    public function count()
    {
        return count($this->data);
    }

    /**
     * Get object iterator
     *
     * @return \ArrayIterator
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->data);
    }

    /**
     * Dynamically access object proxies.
     *
     * @param  string  $key
     * @return mixed
     */
    public function __get($key)
    {
        $value = null;

        if ($this->has($key)) {
            $value = $this->get($key);
        } else {
            // 支持对象访问方式的驼峰命名
            $key = snake($key);

            if ($this->has($key)) {
                $value = $this->get($key);
            } 
        }

        if (is_array($value)) {
            $value = new self($value);
        }

        return $value;
    }

    public function __toString()
    {
        return json_encode($this->data);
    }
}
