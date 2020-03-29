<?php
declare(strict_types=1);

namespace Seo;

use Banana\Plugin\BasePlugin;
use Cake\Core\PluginApplicationInterface;
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
class Plugin extends BasePlugin implements EventListenerInterface
{
    public function bootstrap(PluginApplicationInterface $app): void
    {
        EventManager::instance()->on($this);
    }

    public function routes(\Cake\Routing\RouteBuilder $routes): void
    {
        //Router::extensions(['xml']);
        // robots.txt
        $routes->connect(
            '/robots.txt',
            ['plugin' => 'Seo', 'controller' => 'Robots', 'action' => 'index'],
            ['_name' => 'seo:robots']
        );

        // sitemap.xml
        $routes->connect(
            '/sitemap',
            ['plugin' => 'Seo', 'controller' => 'Sitemap', 'action' => 'index'],
            ['_name' => 'seo:sitemap', '_ext' => ['xml']]
        );

        // paged sitemaps
        $routes->connect(
            '/sitemap-:sitemap-:page',
            ['plugin' => 'Seo', 'controller' => 'Sitemap', 'action' => 'sitemap'],
            ['pass' => ['sitemap', 'page'], '_ext' => ['xml']]
        );
        $routes->connect(
            '/sitemap-:sitemap',
            ['plugin' => 'Seo', 'controller' => 'Sitemap', 'action' => 'sitemap'],
            ['pass' => ['sitemap'], '_ext' => ['xml']]
        );
    }

    public function backendRoutes(RouteBuilder $routes)
    {
        $routes->fallbacks('DashedRoute');
    }

    /**
     * @return array
     */
    public function implementedEvents(): array
    {
        return [
            'Settings.build' => 'buildSettings',
            'Backend.Menu.build.admin_primary' => 'buildBackendMenu',
        ];
    }

    /**
     * @param \Cake\Event\Event $event
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
     * @param \Cake\Event\Event $event
     * @return void
     */
    public function buildBackendMenu(Event $event, \Banana\Menu\Menu $menu)
    {
        /*
        $menu->addItem([
            'title' => 'Seo',
            'url' => ['plugin' => 'Seo', 'controller' => 'Dashboard', 'action' => 'index'],
            'data-icon' => 'line-chart',
        ]);
        */
    }
}
