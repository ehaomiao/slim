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
use Haomiao\Slim\DataObject;

/**
 * Handler for 404.
 * 
 * @package Sinpe\Framework
 * @since   1.0.0
 */
class NotFoundHandler extends BadRequestHandler
{
    /**
     * Initliazation after construction.
     *
     * @return void
     */
    public function __init()
    {
        $this->registerRenderers([
            static::CONTENT_TYPE_HTML => function ($request, &$response) {
                $renderer = new NotFoundHtmlRenderer();
                $renderer->setHomeUrl((string)($request->getUri()->withPath('')->withQuery('')->withFragment('')));
                $response = $renderer->process(new DataObject($this->getRendererContext($response)));
                return $renderer->getOutput();
            }
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

        $response = $response->withStatus(404);

        return $output;
    }

}
