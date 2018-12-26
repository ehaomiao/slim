<?php
/*
 * This file is part of the long/framework package.
 *
 * (c) Sinpe <support@sinpe.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Haomiao\Slim\Exception;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * 404.
 * 
 * @package Sinpe\Framework
 * @since   1.0.0
 */
class NotFound extends BadRequest
{
    /**
     * __construct
     */
    public function __construct(ServerRequestInterface $request, ResponseInterface $response) 
    {        
        parent::__construct('Not found', -404, $request, $response);
    }

}
