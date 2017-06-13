<?php

namespace Seo;

use Cake\Event\Event;
use Cake\Event\EventListenerInterface;
use Cake\Routing\Router;

/**
 * Class SeoPlugin
 *
 * @package Seo
 */
class SeoPlugin implements EventListenerInterface
{

    /**
     * @return array
     */
    public function implementedEvents()
    {
        return [
            'Settings.get' => 'getSettings',
            'Backend.Menu.get' => 'getBackendMenu',
            'Backend.Routes.build' => 'buildBackendRoutes'
        ];
    }

    /**
     * @param Event $event
     * @return void
     */
    public function getSettings(Event $event)
    {
        $event->result['Seo'] = [
            'Google.Analytics.trackingId' => [
                'type' => 'string',
            ],
        ];
    }

    /**
     * Build backend plugin routes
     * @return void
     */
    public function buildBackendRoutes()
    {
        Router::scope('/seo/admin', ['plugin' => 'Seo', 'prefix' => 'admin', '_namePrefix' => 'seo:admin:'], function ($routes) {
            $routes->fallbacks('DashedRoute');
        });
    }

    /**
     * @param Event $event
     * @return void
     */
    public function getBackendMenu(Event $event)
    {
        $event->subject()->addItem([
            'title' => 'Seo',
            'url' => ['plugin' => 'Seo', 'controller' => 'Dashboard', 'action' => 'index'],
            'data-icon' => 'line-chart',
        ]);
    }
}
