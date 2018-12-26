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

use Psr\Http\Message\ResponseInterface;

/**
 * Handler for 400.
 * 
 * @package Sinpe\Framework
 * @since   1.0.0
 */
class BadRequestHandler extends MessageHandler
{
    /**
     * Handler procedure.
     *
     * @return string
     */
    protected function process(ResponseInterface &$response)
    {
        $response = $response->withStatus(400);

        return $this->rendererProcess($response);
    }

}
