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
 * Handler for 405.
 * 
 * @package Sinpe\Framework
 * @since   1.0.0
 */
class MethodNotAllowedHandler extends BadRequestHandler
{
    /**
     * Initliazation after construction.
     *
     * @return void
     */
    public function __init()
    {
        $this->registerRenderers([
            static::CONTENT_TYPE_HTML => MethodNotAllowedHtmlRenderer::class
        ]);
    }

    /**
     * Handler procedure.
     *
     * @return string
     */
    protected function process(ResponseInterface &$response)
    {
        if ($this->request->getMethod() === 'OPTIONS') {
            $contentType = 'text/plain';
            $output = '';
        } else {
            $contentType = $this->determineContentType();
            $output = $this->rendererProcess($response);
        }

        $response = $response->withStatus(405)
            ->withHeader('Allow', implode(', ', $this->thrown->getAllowedMethods()));

        return $output;
    }

}
