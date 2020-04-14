<?php
namespace Seo\Test\App;

use Cake\Http\BaseApplication;
use Cake\Http\MiddlewareQueue;
use Cake\Routing\Middleware\RoutingMiddleware;
use Cake\Routing\RouteBuilder;

class Application extends BaseApplication
{
    /**
     * {@inheritDoc}
     */
    public function bootstrap(): void
    {
        $this->addPlugin('Seo');
    }

    /**
     * {@inheritDoc}
     */
    public function routes(RouteBuilder $routes): void
    {
    }

    /**
     * @inheritDoc
     */
    public function middleware(MiddlewareQueue $middlewareQueue): MiddlewareQueue
    {
        $middlewareQueue
            ->add(new RoutingMiddleware($this));

        return $middlewareQueue;
    }
}
