<?php
/*
 * This file is part of the long/container package.
 *
 * (c) Sinpe <support@sinpe.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Haomiao\Slim;

use Psr\Container\ContainerInterface as PsrContainerInterface;

/**
 * Facade base class.
 * 
 * @package Sinpe\Container
 * @since   1.0.0
 */
abstract class Facade
{
    /**
     * The container instance being facaded.
     *
     * @var PsrContainerInterface
     */
    protected static $container;

    /**
     * Get the root object behind the facade.
     *
     * @return mixed
     */
    public static function getFacadeRoot()
    {
        return static::resolveFacadeInstance(static::getFacadeAccessor());
    }

    /**
     * Get the registered name of the component.
     *
     * @return string
     *
     * @throws \RuntimeException
     */
    protected static function getFacadeAccessor()
    {
        throw new \RuntimeException(i18n('Facade does not implement getFacadeAccessor method.'));
    }

    /**
     * Resolve the facade root instance from the container.
     *
     * @param string|object $name
     *
     * @return mixed
     */
    protected static function resolveFacadeInstance($name)
    {
        if (is_object($name)) {
            return $name;
        }

        return static::$container->get($name);
    }

    /**
     * Set the application instance.
     *
     * @param PsrContainerInterface $container
     */
    public static function setContainer(PsrContainerInterface $container)
    {
        static::$container = $container;
    }

    /**
     * Handle dynamic, static calls to the object.
     *
     * @param string $method
     * @param array  $args
     *
     * @return mixed
     *
     * @throws \RuntimeException
     */
    public static function __callStatic($method, $args)
    {
        $instance = static::getFacadeRoot();

        if (!$instance) {
            throw new \RuntimeException(i18n('A facade root "%s" not exists.',static::getFacadeAccessor()));
        }

        return $instance->$method(...$args);
    }
}
