<?php
namespace Seo\Test\TestApp;

use Cake\Http\BaseApplication;
use Cake\Http\MiddlewareQueue;

class Application extends BaseApplication
{
    public function bootstrap(): void
    {
        parent::bootstrap();

        $this->addPlugin('Seo');
    }

    /**
     * @inheritDoc
     */
    public function middleware(MiddlewareQueue $middlewareQueue): MiddlewareQueue
    {
        return $middlewareQueue;
    }
}
