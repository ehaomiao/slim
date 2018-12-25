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
 * The HTML renderer for route not found exception.
 * 
 * @package Sinpe\Framework
 * @since   1.0.0
 */
class NotFoundHtmlRenderer extends Renderer
{
    /**
     * @var string
     */
    private $homeUrl;

    /**
     * Set home url.
     *
     * @param string $homeUrl
     * @return void
     */
    public function setHomeUrl(string $homeUrl)
    {
        $this->homeUrl = $homeUrl;
    }

    /**
     * Process a handler context and assign result to "output" property.
     *
     * @return ResponseInterface
     */
    public function process(DataObject $context) : ResponseInterface
    {
        $this->output = <<<END
<html>
    <head>
        <title>Page Not Found</title>
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
            strong{
                display:inline-block;
                width:65px;
            }
        </style>
    </head>
    <body>
        <h1>Page Not Found</h1>
        <p>
            The page you are looking for could not be found. Check the address bar
            to ensure your URL is spelled correctly. If all else fails, you can
            visit our home page at the link below.
        </p>
        <a href='{$this->homeUrl}'>Visit the Home Page</a>
    </body>
</html>
END;

        return $context->response;
    }

}
