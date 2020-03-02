<?php

namespace Seo;

use Banana\Application;
use Banana\Plugin\BasePlugin;
use Cake\Event\Event;
use Cake\Event\EventListenerInterface;
use Cake\Event\EventManager;
use Cake\Routing\RouteBuilder;
use Settings\SettingsManager;

/**
 * Class SeoPlugin
 *
 * @package Seo
 */
class SeoPlugin extends BasePlugin implements EventListenerInterface
{
    protected $_name = "Seo";

    public function bootstrap(Application $app)
    {
        parent::bootstrap($app);

        EventManager::instance()->on($this);
    }

    public function backendRoutes(RouteBuilder $routes)
    {
        $routes->fallbacks('DashedRoute');
    }

    /**
     * @return array
     */
    public function implementedEvents()
    {
        return [
            'Settings.build' => 'buildSettings',
            'Backend.Menu.build.admin_primary' => 'buildBackendMenu',
        ];
    }

    /**
     * @param Event $event
     */
    public function buildSettings(Event $event, SettingsManager $settingsManager)
    {
        $settingsManager->add('Seo', [
            'Google.Analytics.trackingId' => [
                'type' => 'string',
            ],
        ]);
    }

    /**
     * @param Event $event
     * @return void
     */
    public function buildBackendMenu(Event $event, \Banana\Menu\Menu $menu)
    {
        $menu->addItem([
            'title' => 'Seo',
            'url' => ['plugin' => 'Seo', 'controller' => 'Dashboard', 'action' => 'index'],
            'data-icon' => 'line-chart',
        ]);
    }
}
