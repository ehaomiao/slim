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

/**
 * A renderer base class.
 * 
 * @package Sinpe\Framework
 * @since   1.0.0
 */
abstract class Renderer implements RendererInterface
{
    /**
     * The renderer processed result.
     * 
     * Please assign this variable in then "process" method!
     *
     * @var string
     */
    protected $output;

    /**
     * Return the renderer processed result.
     *
     * @return string
     */
    public function getOutput()
    {
        return $this->output;
    }
}
