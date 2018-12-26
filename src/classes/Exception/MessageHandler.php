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

use Haomiao\Slim\DataObject;

/**
 * Handler for runtime error.
 * 
 * @package Sinpe\Framework
 * @since   1.0.0
 */
class MessageHandler extends Handler
{
    /**
     * Create the content will be rendered.
     *
     * @return DataObject
     */
    protected function getContentOfHandler()
    {
        $error = [
            'code' => $this->thrown->getCode(),
            'message' => $this->thrown->getMessage()
        ];

        $data = $this->thrown->getData();

        if (!empty($data)) {
            $error['data'] = $data;
        }

        return new DataObject($error);
    }

}
