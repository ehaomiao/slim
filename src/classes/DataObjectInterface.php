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
 * DataObject Interface
 *
 * @package Sinpe\Framework
 * @since   1.0.0
 */
interface DataObjectInterface extends \ArrayAccess, \Countable, \IteratorAggregate
{
    /**
     * Set an item in the object by key.
     *
     * @param  mixed  $key
     * @param  mixed  $value
     * @return $this
     */
    public function set($key, $value);

    /**
     * Get an item from the object by key.
     *
     * @param  mixed  $key
     * @param  mixed  $default
     * @return mixed
     */
    public function get($key, $default = null);

    // public function replace(array $items);

    // /**
    //  * Undocumented function
    //  *
    //  * @return void
    //  */
    // public function all();

    /**
     * Determine if an item exists in the object by key.
     *
     * @param  mixed  $key
     * @return bool
     */
    public function has($key);


    // public function remove($key);

    /**
     * Remove all items from object
     */
    public function reset();
}
