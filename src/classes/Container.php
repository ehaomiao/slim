<?php
/*
 * This file is part of ehaomiao/slim.
 *
 * (c) Haomiao Inc. <dev@ehaomiao.com>
 *
 * For the full copyright and license information, please view the "LICENSE.md"
 * file that was distributed with this source code.
 */

namespace Haomiao\Slim;

use Psr\Container\ContainerInterface as PsrContainerInterface;
use Illuminate\Container\Container as Base;

/**
 * 增加slim原本默认配置
 *
 *  - settings: an array or instance of \ArrayAccess
 *  - environment: an instance of \Slim\Interfaces\Http\EnvironmentInterface
 *  - request: an instance of \Psr\Http\Message\ServerRequestInterface
 *  - response: an instance of \Psr\Http\Message\ResponseInterface
 *  - router: an instance of \Slim\Interfaces\RouterInterface
 *  - foundHandler: an instance of \Slim\Interfaces\InvocationStrategyInterface
 *  - errorHandler: a callable with the signature: function($request, $response, $exception)
 *  - notFoundHandler: a callable with the signature: function($request, $response)
 *  - notAllowedHandler: a callable with the signature: function($request, $response, $allowedHttpMethods)
 *  - callableResolver: an instance of \Slim\Interfaces\CallableResolverInterface
 *
 * @property-read array settings
 * @property-read \Slim\Interfaces\Http\EnvironmentInterface environment
 * @property-read \Psr\Http\Message\ServerRequestInterface request
 * @property-read \Psr\Http\Message\ResponseInterface response
 * @property-read \Slim\Interfaces\RouterInterface router
 * @property-read \Slim\Interfaces\InvocationStrategyInterface foundHandler
 * @property-read callable errorHandler
 * @property-read callable notFoundHandler
 * @property-read callable notAllowedHandler
 * @property-read \Slim\Interfaces\CallableResolverInterface callableResolver
 */
class Container extends Base
{
    /**
     * Create new container
     *
     * @param array $values The parameters or objects.
     */
    public function __construct()
    {
        Facade::setContainer($this);

		class_alias(ContainerFacade::class, 'Container');

        if (!$this->has(PsrContainerInterface::class)) {
            $this->instance(PsrContainerInterface::class, $this);
        }
        
        $this->registerDefaultServices();

        static::setInstance($this);
    }

    /**
     * This function registers the default services that Slim needs to work.
     *
     * All services are shared - that is, they are registered such that the
     * same instance is returned on subsequent calls.
     *
     * @return void
     */
    private function registerDefaultServices()
    {
        $defaultProvider = new DefaultServicesProvider();

        $defaultProvider->register($this);
    }

    /**
     * Instantiate a concrete instance of the given type.
     * 
     * 新增__init调用
     *
     * @param  string  $concrete
     * @return mixed
     *
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function build($concrete)
    {
        $object = parent::build($concrete);

        if (method_exists($object, 'setContainer')) {
            $object->setContainer($this);
        }

        if (method_exists($object, '__init')) {
            $object->__init();
        }

        return $object;
    }

    /**
     * Undocumented function
     *
     * @param string $name
     * 
     * @return boolean
     */
    public function __isset($name)
    {
        return $this->has($name);
    }
}
