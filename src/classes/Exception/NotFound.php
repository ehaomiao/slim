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
     *
     * @param array $context
     */
    public function __construct(array $context = [])
    {
        parent::__construct('Not found', -404, null, $context);
    }

}
