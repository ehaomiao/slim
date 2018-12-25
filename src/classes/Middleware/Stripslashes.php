<?php
/*
 * This file is part of ehaomiao/slim.
 *
 * (c) Haomiao Inc. <dev@ehaomiao.com>
 *
 * For the full copyright and license information, please view the "LICENSE.md"
 * file that was distributed with this source code.
 */

namespace Haomiao\Slim\Middleware;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;

class Stripslashes
{
    /**
     * __construct
     */
    public function __construct()
    {
    }

    /**
     * Invoke cache middleware
     *
     * @param  ServerRequestInterface  $request  A PSR7 request object
     * @param  ResponseInterface $response A PSR7 response object
     * @param  callable          $next     The next middleware callable
     *
     * @return ResponseInterface           A PSR7 response object
     */
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, callable $next)
    {
        if ($request->getMethod() === 'POST' &&
            in_array($request->getMediaType(), ['application/x-www-form-urlencoded', 'multipart/form-data'])) {
            $params = $request->getParams();

            $params = $this->deepStrip($params);

            $request = $request->withParsedBody($params);
        }

        $response = $next($request, $response);

        return $response;
    }

    /**
     * 深度strip
     *
     * @return array
     */
    protected function deepStrip($data)
    {
        $data = is_array($data) ? array_map([$this, 'deepStrip'], $data) : stripslashes(urldecode($data));

        return $data;
    }

}