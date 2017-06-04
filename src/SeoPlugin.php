<?php

namespace Seo;

use Cake\Event\Event;
use Cake\Event\EventListenerInterface;
use Cake\Routing\Router;

class SeoPlugin implements EventListenerInterface
{

    /**
     * Returns a list of events this object is implementing. When the class is registered
     * in an event manager, each individual method will be associated with the respective event.
     *
     * @see EventListenerInterface::implementedEvents()
     * @return array associative array or event key names pointing to the function
     * that should be called in the object when the respective event is fired
     */
    public function implementedEvents()
    {
        return [
            'Settings.get' => 'getSettings',
            'Backend.Menu.get' => 'getBackendMenu',
            'Backend.Routes.build' => 'buildBackendRoutes'
        ];
    }

    public function getSettings(Event $event)
    {
        $event->result['Seo'] = [
            'Google.Analytics.trackingId' => [
                'type' => 'string',
            ],
        ];
    }

    public function buildBackendRoutes()
    {
        Router::scope('/seo/admin', ['plugin' => 'Seo', 'prefix' => 'admin', '_namePrefix' => 'seo:admin:'], function($routes) {
            $routes->fallbacks('DashedRoute');
        });
    }

    public function getBackendMenu(Event $event)
    {
        $event->subject()->addItem([
            'title' => 'Seo',
            'url' => ['plugin' => 'Seo', 'controller' => 'Dashboard', 'action' => 'index'],
            'data-icon' => 'line-chart',
        ]);
    }
}