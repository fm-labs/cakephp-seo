<?php
declare(strict_types=1);

namespace Seo;

use Cake\Core\BasePlugin;
use Cake\Core\PluginApplicationInterface;
use Cake\Event\Event;
use Cake\Event\EventListenerInterface;
use Cake\Event\EventManager;
use Settings\SettingsManager;

/**
 * Class SeoPlugin
 *
 * @package Seo
 */
class Plugin extends BasePlugin implements EventListenerInterface
{
    /**
     * {@inheritDoc}
     */
    public function bootstrap(PluginApplicationInterface $app): void
    {
        EventManager::instance()->on($this);
    }

    /**
     * {@inheritDoc}
     */
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

        // stylesheets
        $routes->connect(
            '/sitemap/style/:name',
            ['plugin' => 'Seo', 'controller' => 'Sitemap', 'action' => 'style'],
            ['pass' => ['name'], '_ext' => ['xsl']]
        );
    }

    /**
     * {@inheritDoc}
     */
    public function implementedEvents(): array
    {
        return [
            'Settings.build' => 'buildSettings',
        ];
    }

    /**
     * @param \Cake\Event\Event $event Event object
     * @param \Settings\SettingsManager $settingsManager SettingsManager object
     * @return void
     */
    public function buildSettings(Event $event, SettingsManager $settingsManager): void
    {
        $settingsManager->add('Seo', [
            'Google.Analytics.trackingId' => [
                'type' => 'string',
            ],
        ]);
    }
}
