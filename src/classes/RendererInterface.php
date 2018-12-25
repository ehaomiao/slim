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

use Psr\Http\Message\ResponseInterface;
use Haomiao\Slim\DataObject;

/**
 * The renderer interface.
 * 
 * @package Sinpe\Framework
 * @since   1.0.0
 */
interface RendererInterface
{
    /**
     * Process a handler context and assign result to "output" property.
     *
     * @return ResponseInterface
     */
    public function process(DataObject $context) : ResponseInterface;

    /**
     * Return the renderer processed result.
     *
     * @return string
     */
    public function getOutput();

}
