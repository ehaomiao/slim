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
 * 405.
 * 
 * @package Sinpe\Framework
 * @since   1.0.0
 */
class MethodNotAllowed extends BadRequest
{
    /**
     * HTTP methods allowed
     *
     * @var string[]
     */
    protected $allowedMethods;

    /**
     * __construct
     *
     * @param string[] $allowedMethods
     * @param mixed $previous
     */
    public function __construct(
        array $allowedMethods, 
        ServerRequestInterface $request, 
        ResponseInterface $response
    ) {
        $this->allowedMethods = $allowedMethods;

        parent::__construct(
            'Method not allowed. Must be one of: ' . implode(', ', $allowedMethods), 
            -405, 
            $request, 
            $response
        );
    }

    /**
     * Get allowed methods
     *
     * @return string[]
     */
    public function getAllowedMethods()
    {
        return $this->allowedMethods;
    }
}
