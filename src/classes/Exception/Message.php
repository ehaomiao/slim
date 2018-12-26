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
 * runtime error.
 * 
 * @package Sinpe\Framework
 * @since   1.0.0
 */
class Message extends \RuntimeException
{
    use AllTrait;

    /**
     * @var array
     */
    protected $data = [];

    /**
     * __construct
     *
     * @param string $message
     * @param mixed $code
     * @param mixed $previous
     * @param array $data
     */
    public function __construct(
        string $message,
        $code = null,
        $previous = null,
        $data = []
    ) {
        $this->setRequestAndResponse(func_get_args());

        if (!is_int($code)) {
            if (!$code instanceof \Throwable) {
                $data = $code;
            } else {
                $previous = $code;
            }
            $code = $this->getDefaultCode();
        }

        if (!$previous instanceof \Throwable) {
            $data = $previous;
            $previous = null;
        }

        if (!is_array($data)) {
            $data = [];
        }

        $this->data = $data;

        parent::__construct($message, $code, $previous);
    }

    /**
     * Attached data.
     *
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * Return default code.
     *
     * @return integer
     */
    protected function getDefaultCode()
    {
        return -1;
    }
}
