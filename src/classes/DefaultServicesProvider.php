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

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Handlers\PhpError;
use Slim\Handlers\Error;

use Slim\Http\Environment;
use Slim\Http\Headers;
use Slim\Http\Request;
use Slim\Http\Response;
use Slim\Interfaces\CallableResolverInterface;
use Slim\Interfaces\Http\EnvironmentInterface;
use Slim\Interfaces\InvocationStrategyInterface;
use Slim\Interfaces\RouterInterface;
use Slim\Router;

use Haomiao\Slim\Route\ArgsNamed;
use Haomiao\Slim\Exception\MethodNotAllowedHandler;
use Haomiao\Slim\Exception\NotFoundHandler;

/**
 * Slim's default Service Provider.
 */
class DefaultServicesProvider
{
    /**
     * Register Slim's default services.
     *
     * @param Container $container A DI container implementing ArrayAccess and container-interop.
     */
    public function register($container)
    {
        if (!isset($container['environment'])) {
            /**
             * This service MUST return a shared instance
             * of \Slim\Interfaces\Http\EnvironmentInterface.
             *
             * @return EnvironmentInterface
             */
            $container->singleton(
                'environment',
                function () {
                    return new Environment($_SERVER);
                }
            );
        }

        if (!isset($container['settings'])) {

            /**
             * Setting
             *
             * @param Container $container
             *
             * @return Setting
             */
            $container['settings'] = function () {
                return new Setting();
            };
        }

        if (!isset($container['request'])) {
            /**
             * PSR-7 Request object
             *
             * @param Container $container
             *
             * @return ServerRequestInterface
             */
            $container->singleton(
                'request',
                function ($container) {
                    return Request::createFromEnvironment($container->get('environment'));
                }
            );
        }

        if (!isset($container['response'])) {
            /**
             * PSR-7 Response object
             *
             * @param Container $container
             *
             * @return ResponseInterface
             */
            $container->singleton(
                'response',
                function ($container) {
                    $headers = new Headers(['Content-Type' => 'text/html; charset=UTF-8']);
                    $response = new Response(200, $headers);

                    return $response->withProtocolVersion($container->get('settings')['httpVersion']);
                }
            );
        }

        if (!isset($container['router'])) {
            /**
             * This service MUST return a SHARED instance
             * of \Slim\Interfaces\RouterInterface.
             *
             * @param Container $container
             *
             * @return RouterInterface
             */
            $container->singleton(
                'router',
                function ($container) {

                    $routerCacheFile = false;

                    if (isset($container->get('settings')['routerCacheFile'])) {
                        $routerCacheFile = $container->get('settings')['routerCacheFile'];
                    }

                    $router = (new Router)->setCacheFile($routerCacheFile);
                    if (method_exists($router, 'setContainer')) {
                        $router->setContainer($container);
                    }

                    return $router;
                }
            );
        }

        if (!isset($container['foundHandler'])) {
            /**
             * This service MUST return a SHARED instance
             * of \Slim\Interfaces\InvocationStrategyInterface.
             *
             * @return InvocationStrategyInterface
             */
            $container->singleton(
                'foundHandler',
                function ($container) {
                    return new ArgsNamed($container);
                    // return new RequestResponse;
                }
            );
        }

        if (!isset($container['phpErrorHandler'])) {
            /**
             * This service MUST return a callable
             * that accepts three arguments:
             *
             * 1. Instance of \Psr\Http\Message\ServerRequestInterface
             * 2. Instance of \Psr\Http\Message\ResponseInterface
             * 3. Instance of \Error
             *
             * The callable MUST return an instance of
             * \Psr\Http\Message\ResponseInterface.
             *
             * @param Container $container
             *
             * @return callable
             */
            $container->singleton(
                'phpErrorHandler',
                function ($container) {
                    return new PhpError($container->get('settings')['displayErrorDetails']);
                }
            );
        }

        if (!isset($container['errorHandler'])) {
            /**
             * This service MUST return a callable
             * that accepts three arguments:
             *
             * 1. Instance of \Psr\Http\Message\ServerRequestInterface
             * 2. Instance of \Psr\Http\Message\ResponseInterface
             * 3. Instance of \Exception
             *
             * The callable MUST return an instance of
             * \Psr\Http\Message\ResponseInterface.
             *
             * @param Container $container
             *
             * @return callable
             */
            $container->singleton(
                'errorHandler',
                function ($container) {
                    return new Error(
                        $container->get('settings')['displayErrorDetails']
                    );
                }
            );
        }

        // if (!isset($container['notFoundHandler'])) {
        //     /**
        //      * This service MUST return a callable
        //      * that accepts two arguments:
        //      *
        //      * 1. Instance of \Psr\Http\Message\ServerRequestInterface
        //      * 2. Instance of \Psr\Http\Message\ResponseInterface
        //      *
        //      * The callable MUST return an instance of
        //      * \Psr\Http\Message\ResponseInterface.
        //      *
        //      * @return callable
        //      */
        //     $handler = $container->make(NotFoundHandler::class);

        //     $container->instance('notFoundHandler', $handler);
        // }

        // if (!isset($container['notAllowedHandler'])) {
        //     /**
        //      * This service MUST return a callable
        //      * that accepts three arguments:
        //      *
        //      * 1. Instance of \Psr\Http\Message\ServerRequestInterface
        //      * 2. Instance of \Psr\Http\Message\ResponseInterface
        //      * 3. Array of allowed HTTP methods
        //      *
        //      * The callable MUST return an instance of
        //      * \Psr\Http\Message\ResponseInterface.
        //      *
        //      * @return callable
        //      */
        //     $handler = $container->make(MethodNotAllowedHandler::class);

        //     $container->instance('notAllowedHandler', $handler);
        // }

        if (!isset($container['clientErrorHandler'])) {
            /**
             *
             * @return callable
             */
            $container->singleton(
                'clientErrorHandler',
                function () {
                    return new ClientError;
                }
            );
        }

        if (!isset($container['callableResolver'])) {
            /**
             * Instance of \Slim\Interfaces\CallableResolverInterface
             *
             * @param Container $container
             *
             * @return CallableResolverInterface
             */
            $container->singleton(
                'callableResolver',
                function ($container) {
                    return new CallableResolver($container);
                }
            );
        }
    }
}
