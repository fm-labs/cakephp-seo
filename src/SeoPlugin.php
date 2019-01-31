<?php

namespace Seo;

use Cake\Event\Event;
use Cake\Event\EventListenerInterface;
use Cake\Routing\Router;
use Settings\SettingsManager;

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
            'Settings.build' => 'buildSettings',
            'Backend.Menu.build' => 'buildBackendMenu',
            'Backend.Routes.build' => 'buildBackendRoutes'
        ];
    }

    /**
     * @param Event $event
     */
    public function buildSettings(Event $event)
    {
        if ($event->subject() instanceof SettingsManager) {

            $event->subject()->add('Seo', [
                'Google.Analytics.trackingId' => [
                    'type' => 'string',
                ],
            ]);
        }
    }

    /**
     * Build backend plugin routes
     * @return void
     */
    public function buildBackendRoutes()
    {
        if (\Configure::read('Debug')) {
            Router::scope('/seo/admin', ['plugin' => 'Seo', 'prefix' => 'admin', '_namePrefix' => 'seo:admin:'], function ($routes) {
                $routes->fallbacks('DashedRoute');
            });
        }
    }

    /**
     * @param Event $event
     * @return void
     */
    public function buildBackendMenu(Event $event)
    {
        $event->subject()->addItem([
            'title' => 'Seo',
            'url' => ['plugin' => 'Seo', 'controller' => 'Dashboard', 'action' => 'index'],
            'data-icon' => 'line-chart',
        ]);
    }
}
