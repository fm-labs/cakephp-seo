<?php

namespace Seo\Test\App\Error;

use Cake\Error\ExceptionRendererInterface;
use Cake\Http\Exception\HttpException;
use Cake\Http\Response;
use Cake\Http\ServerRequest;
use Psr\Http\Message\ResponseInterface;

class ExceptionRenderer implements ExceptionRendererInterface
{
    /**
     * @var \Cake\Http\ServerRequest|null
     */
    private $request;

    /**
     * @var \Throwable
     */
    private $error;

    /**
     * Creates the controller to perform rendering on the error response.
     * If the error is a Cake\Core\Exception\Exception it will be converted to either a 400 or a 500
     * code error depending on the code used to construct the error.
     *
     * @param \Throwable $exception Exception.
     * @param \Cake\Http\ServerRequest $request The request if this is set it will be used
     *   instead of creating a new one.
     */
    public function __construct(\Throwable $exception, ?ServerRequest $request = null)
    {
        $this->error = $exception;
        $this->request = $request;
    }

    /**
     * @inheritDoc
     */
    public function render(): ResponseInterface
    {
        $message = "An error occured";

        if ($this->error instanceof HttpException) {
            $message = $this->error->getMessage();
        }

        return new Response([
            'status' => 404,
            'body' => $message,
            'type' => 'text/plain',
        ]);
    }
}
