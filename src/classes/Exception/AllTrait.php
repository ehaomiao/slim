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
 * Exception with context.
 * 
 * @package Sinpe\Framework
 * @since   1.0.0
 */
trait AllTrait
{
    /**
     * A request object
     *
     * @var ServerRequestInterface
     */
    protected $request;

    /**
     * A response object to send to the HTTP client
     *
     * @var ResponseInterface
     */
    protected $response;

    /**
     * __construct
     *
     * @param string $message
     * @param mixed $code
     * @param mixed $previous
     * @param array $context
     */
    public function __construct(
        string $message,
        $code = null,
        $previous = null,
        array $context = []
    ) {
        list($code, $previous, $context) = $this->parseArgs($code, $previous, $context);

        if (isset($context['request'])) {
            $this->request = $context['request'];
        }

        if (isset($context['response'])) {
            $this->response = $context['response'];
        }

        parent::__construct($message, $code, $previous);
    }

    /**
     * Parse arguments.
     *
     * @param mixed $code
     * @param mixed $previous
     * @param array $context
     */
    protected function parseArgs($code = null, $previous = null, array $context = [])
    {
        if (!is_int($code)) {
            if (!$code instanceof \Throwable) {
                $context = $code;
            } else {
                $previous = $code;
            }
            $code = $this->getDefaultCode();
        }

        if (!$previous instanceof \Throwable) {
            $context = $previous;
            $previous = null;
        }

        if (!is_array($context)) {
            $context = [];
        }

        return [$code, $previous, $context];
    }

    /**
     * __get
     *
     * @return mixed
     */
    public function __get($name)
    {
        if (in_array($name, ['request', 'response'])) {
            return $this->{$name};
        }

        throw new \RuntimeException(i18n('Property %s::%s not exist.', get_class($this), $name));
    }

}
