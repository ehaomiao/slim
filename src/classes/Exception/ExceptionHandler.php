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

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Haomiao\Slim\DataObject;
use Haomiao\Slim\SettingInterface;

/**
 * Handler for 500.
 * 
 * @package Sinpe\Framework
 * @since   1.0.0
 */
class ExceptionHandler extends Handler
{
    /**
     * @var Setting
     */
    private $setting;

    /**
     * __construct
     */
    public function __construct(SettingInterface $setting)
    {
        $this->setting = $setting;
    }

    /**
     * Initliazation after construction.
     *
     * @return void
     */
    public function __init()
    {
        $this->registerRenderers([
            static::CONTENT_TYPE_HTML => ExceptionHtmlRenderer::class
        ]);
    }

    /**
     * Write to the error log
     *
     * @return void
     */
    protected function writeToErrorLog()
    {
        $thrown = $this->thrown;

        $message = 'Fault:' . PHP_EOL;

        $message .= $this->renderThrowable($thrown);

        while ($thrown = $thrown->getPrevious()) {
            $message .= PHP_EOL . 'Previous error:' . PHP_EOL;
            $message .= $this->renderThrowable($thrown);
        }

        $message .= PHP_EOL . 'View in rendered output by enabling the "displayErrorDetails" setting.' . PHP_EOL;

        error_log($message);
    }

    /**
     * Render error as Text.
     *
     * @param \Throwable $thrown
     *
     * @return string
     */
    protected function renderThrowable($thrown)
    {
        $text = sprintf('Type: %s' . PHP_EOL, get_class($thrown));

        if ($code = $thrown->getCode()) {
            $text .= sprintf('Code: %s' . PHP_EOL, $code);
        }

        if ($message = $thrown->getMessage()) {
            $text .= sprintf('Message: %s' . PHP_EOL, htmlentities($message));
        }

        if ($file = $thrown->getFile()) {
            $text .= sprintf('File: %s' . PHP_EOL, $file);
        }

        if ($line = $thrown->getLine()) {
            $text .= sprintf('Line: %s' . PHP_EOL, $line);
        }

        if ($trace = $thrown->getTraceAsString()) {
            $text .= sprintf('Trace: %s', $trace);
        }

        return $text;
    }

    /**
     * Returns a CDATA section with the given content.
     *
     * @param  string $content
     * @return string
     */
    private function createCdataSection($content)
    {
        if (in_array($this->determineContentType(), [
            static::CONTENT_TYPE_XML1,
            static::CONTENT_TYPE_XML2
        ])) {
            return sprintf('<![CDATA[%s]]>', str_replace(']]>', ']]]]><![CDATA[>', $content));
        } else {
            return $content;
        }
    }

    /**
     * Create the content will be rendered.
     *
     * @return DataObject
     */
    protected function getContentOfHandler()
    {
        $error = [
            'code' => $this->thrown->getCode(),
            'message' => 'Fault'
        ];

        // 
        if ($this->setting->displayErrorDetails) {

            $thrown = $this->thrown;

            $error['type'] = get_class($thrown);
            $error['message'] = $this->createCdataSection($thrown->getMessage());
            $error['file'] = $thrown->getFile();
            $error['line'] = $thrown->getLine();
            $error['trace'] = $this->createCdataSection($thrown->getTraceAsString());

            while ($thrown = $thrown->getPrevious()) {
                $error['previous'][] = [
                    'type' => get_class($thrown),
                    'code' => $thrown->getCode(),
                    'message' => $this->createCdataSection($thrown->getMessage()),
                    'file' => $thrown->getFile(),
                    'line' => $thrown->getLine(),
                    'trace' => $this->createCdataSection($thrown->getTraceAsString())
                ];
            }
        }

        return new DataObject($error);
    }

    /**
     * Handler procedure.
     *
     * @return string
     */
    protected function process(ResponseInterface &$response)
    {
        // Write to the error log if displayErrorDetails is false
        if (!$this->setting->displayErrorDetails) {
            $this->writeToErrorLog();
        }

        $response = $response->withStatus(500);

        return $this->rendererProcess($response);
    }

    /**
     * Create the renderer context.
     *
     * @return array
     */
    protected function getRendererContext(ResponseInterface $response)
    {
        return [
            'thrown' => $this->thrown,
            'displayErrorDetails' => $this->setting->displayErrorDetails,
            'response' => $response,
            'content' => $this->getContentOfHandler()
        ];
    }

}
