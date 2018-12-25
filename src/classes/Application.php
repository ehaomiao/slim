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

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;

use Slim\Exception\SlimException;
use Slim\Exception\MethodNotAllowedException;
use Slim\Exception\NotFoundException;

use Haomiao\Slim\Exception\ClientMethodNotAllowedException;
use Haomiao\Slim\Exception\ClientRouteNotFoundException;
use Haomiao\Slim\Exception\Exception;
use Haomiao\Slim\Exception\RuntimeException;

/**
 * Application class
 * 
 * 使用illuminate的container，借助其构造器依赖注入能力
 */
abstract class Application extends \Slim\App
{
    /**
     * Create new application
     *
     * @param ContainerInterface|array $container Either a ContainerInterface or an associative array of app settings
     * @throws InvalidArgumentException when no container is provided that implements ContainerInterface
     */
    public function __construct($container = [])
    {
        if (is_array($container)) {
            $container = new Container($container);
        }

        $container->instance(ContainerInterface::class, $container);

        parent::__construct($container);
        // 
        $this->initialize();

        $container->singleton('settings', SettingInterface::class);

        $this->registerRoutes();
    }

    /**
     * initialize
     * 
     * 需要额外的初始化，覆盖此方法
     *
     * @return void
     */
    protected function initialize()
    {
    }

    /**
     * 初始化路由
     *
     * @return void
     */
    protected function registerRoutes()
    {
    }

    /**
     * Call relevant handler from the Container if needed. If it doesn't exist,
     * then just re-throw.
     *
     * @param  \Exception $e
     * @param  ServerRequestInterface $request
     * @param  ResponseInterface $response
     *
     * @return ResponseInterface
     * @throws \Exception if a handler is needed and not found
     */
    protected function handleException(
        \Exception $e,
        ServerRequestInterface $request,
        ResponseInterface $response
    ) {
        // 转换slim抛出的异常
        if ($e instanceof MethodNotAllowedException) {
            $e = new ClientMethodNotAllowedException(
                $e->getAllowedMethods(),
                [
                    'request' => $e->getRequest(),
                    'response' => $e->getResponse()
                ]
            );
        } elseif ($e instanceof NotFoundException) {
            $e = new ClientRouteNotFoundException(
                [
                    'request' => $e->getRequest(),
                    'response' => $e->getResponse()
                ]
            );
        } elseif ($e instanceof SlimException) {
            $e = new RuntimeException(
                $e->getMessage(),
                [
                    'request' => $e->getRequest(),
                    'response' => $e->getResponse()
                ]
            );
        }

        $setting = $this->getContainer()->get('settings');

        foreach ($setting->throwableHandlers as $targetClass => $handlerClass) {
            // 
            if ($e instanceof $targetClass) {

                $container = $this->getContainer();

                $handler = $container->make($handlerClass);

                $handler->setThrowable($e);

                if (method_exists($e, 'getRequest')) {
                    $request = $e->getRequest(); // 兼容slim的exception
                } elseif ($e->request) {
                    $request = $e->request;
                }
                
                if (method_exists($e, 'getResponse')) {
                    $response = $e->getResponse(); // 兼容slim的exception
                } elseif ($e->response) {
                    $response = $e->response;
                }

                try {
                    return $handler->handle($request, $response);
                } catch (\Exception $ex) {
                    $this->handleException($ex, $request, $response);
                }
            }
        }
        
        return parent::handleException($e, $request, $response);
    }

}