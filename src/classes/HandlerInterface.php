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

use UnexpectedValueException;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * A output handler interface.
 * 
 * @package Sinpe\Framework
 * @since   1.0.0
 */
interface HandlerInterface
{
    /**
     * Invoke the handler
     *
     * @return ResponseInterface
     * @throws UnexpectedValueException
     */
    public function handle(
        ServerRequestInterface $request,
        ResponseInterface $response
    ) : ResponseInterface;
}
