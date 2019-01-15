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

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Haomiao\Slim\DataObject;
use Slim\Http\Body;
use Haomiao\Slim\Renderer\Json as JsonRenderer;
use Haomiao\Slim\Renderer\Html as HtmlRenderer;
use Haomiao\Slim\Renderer\Xml as XmlRenderer;

/**
 * The output handler base class.
 * 
 * @package Sinpe\Framework
 * @since   1.0.0
 */
abstract class Handler implements HandlerInterface
{
    const CONTENT_TYPE_JSON = 'application/json';
    const CONTENT_TYPE_HTML = 'text/html';
    const CONTENT_TYPE_XML1 = 'text/xml';
    const CONTENT_TYPE_XML2 = 'application/xml';

    /**
     * @var ServerRequestInterface
     */
    private $request;

    /**
     * @var string
     */
    private $contentType;

    /**
     * Known handled content types
     *
     * @var array
     */
    protected $renderers = [
        self::CONTENT_TYPE_JSON => JsonRenderer::class,
        self::CONTENT_TYPE_XML1 => XmlRenderer::class,
        self::CONTENT_TYPE_XML2 => XmlRenderer::class,
        self::CONTENT_TYPE_HTML => HtmlRenderer::class
    ];

    /**
     * Determine which content type we know about is wanted using Accept header
     *
     * Note: This method is a bare-bones implementation designed specifically for
     * error handling requirements. Consider a fully-feature solution such
     * as willdurand/negotiation for any other situation.
     *
     * @return string
     */
    protected function determineContentType()
    {
        if (empty($this->contentType)) {

            $acceptHeader = $this->request->getHeaderLine('Accept');

            $defaultContentTypes = array_keys($this->renderers);

            $selectedContentTypes = array_intersect(explode(',', $acceptHeader), $defaultContentTypes);

            if (count($selectedContentTypes)) {
                $this->contentType = current($selectedContentTypes);
            } else {
                // handle +json and +xml specially
                if (preg_match('/\+(json|xml)/', $acceptHeader, $matches)) {
                    //
                    $mediaType = 'application/' . $matches[1];
                    if (in_array($mediaType, $defaultContentTypes)) {
                        $this->contentType = $mediaType;
                    }
                }
            }

            if (empty($this->contentType)) {
                $this->contentType = self::CONTENT_TYPE_HTML;
            }
        }

        return $this->contentType;
    }

    /**
     * Invoke the handler
     *
     * @param  ServerRequestInterface $request
     * @param  ResponseInterface $response
     * 
     * @return ResponseInterface
     * @throws \UnexpectedValueException
     */
    final public function handle(
        ServerRequestInterface $request,
        ResponseInterface $response
    ) : ResponseInterface {

        $this->request = $request;

        $output = $this->process($response);

        $body = new Body(fopen('php://temp', 'r+'));

        $body->write($output);

        return $response->withHeader('Content-Type', $this->determineContentType())
            ->withBody($body);
    }

    /**
     * Handler procedure.
     *
     * @return ResponseInterface
     */
    protected function process(ResponseInterface &$response)
    {
        return $this->rendererProcess($response);
    }

    /**
     * Handler dispatchs a renderer to render the content.
     *
     * @return string
     * @throws \UnexpectedValueException
     */
    final protected function rendererProcess(ResponseInterface &$response)
    {
        $contentType = $this->determineContentType();

        if (array_key_exists($contentType, $this->renderers)) {

            $renderer = $this->renderers[$contentType];

            if ($renderer instanceof \Closure) {
                /*
                renderer需要依赖时，注入通过handler引入，再用此方式调用renderer
                比如：依赖Setting
                function ($response, $content) use($setting) {
                    $renderer = new $renderer($setting);
                    $response = $renderer->process(new DataObject($content));
                    return $renderer->getOutput();
                }
                 */
                // $response 使用引用
                $output = $renderer($this->request, $response, $this->getContentOfHandler());
            } else {
                $renderer = new $renderer;
                $response = $renderer->process(new DataObject($this->getRendererContext($response)));
                $output = $renderer->getOutput();
            }
        } else {
            throw new \UnexpectedValueException('Cannot render unknown content type ' . $contentType);
        }

        return $output;
    }

    /**
     * Register more renderers.
     * you can alse use this method to override the default renderer.
     *
     * @return void
     */
    protected function registerRenderers(array $renderers)
    {
        $this->renderers = array_merge($this->renderers, $renderers);
    }

    /**
     * Create the renderer context.
     *
     * @return array
     */
    protected function getRendererContext(ResponseInterface $response)
    {
        return [
            'response' => $response,
            'content' => $this->getContentOfHandler()
        ];
    }

    /**
     * Create the content will be rendered.
     *
     * @return DataObject
     */
    abstract protected function getContentOfHandler();

    /**
     * __get
     *
     * @return mixed
     */
    public function __get($name)
    {
        if (in_array($name, ['request'])) {
            return $this->{$name};
        }

        throw new \RuntimeException(i18n('Property %s::%s not exist.', get_class($this), $name));
    }

}
