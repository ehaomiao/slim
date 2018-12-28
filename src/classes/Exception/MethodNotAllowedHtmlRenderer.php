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
use Haomiao\Slim\Renderer;

/**
 * The HTML renderer for method not allowed exception.
 * 
 * @package Sinpe\Framework
 * @since   1.0.0
 */
class MethodNotAllowedHtmlRenderer extends Renderer
{
    /**
     * Process a handler context and assign result to "output" property.
     *
     * @return ResponseInterface
     */
    public function process(DataObject $context) : ResponseInterface
    {
        $allow = implode(', ', $context->thrown->getAllowedMethods());

        $this->output = <<<END
<html>
    <head>
        <title>Method not allowed</title>
        <style>
            body{
                margin:0;
                padding:30px;
                font:12px/1.5 Helvetica,Arial,Verdana,sans-serif;
            }
            h1{
                margin:0;
                font-size:48px;
                font-weight:normal;
                line-height:48px;
            }
        </style>
    </head>
    <body>
        <h1>Method not allowed</h1>
        <p>Method not allowed. Must be one of: <strong>$allow</strong></p>
    </body>
</html>
END;

        return $context->response;
    }

}
