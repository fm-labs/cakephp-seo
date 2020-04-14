<?php
declare(strict_types=1);

namespace Seo;

use Cake\Core\BasePlugin;
use Cake\Core\PluginApplicationInterface;
use Cake\Routing\RouteBuilder;

/**
 * Class SeoPlugin
 *
 * @package Seo
 */
class Plugin extends BasePlugin
{
    /**
     * {@inheritDoc}
     */
    public function bootstrap(PluginApplicationInterface $app): void
    {
    }

    /**
     * {@inheritDoc}
     */
    public function routes(RouteBuilder $routes): void
    {
        // robots
        //Router::extensions(['txt']);
        $routes->connect(
            '/robots',
            ['plugin' => 'Seo', 'controller' => 'Robots', 'action' => 'index'],
            ['_name' => 'seo:robots', '_ext' => ['txt']]
        );

        // sitemap
        //Router::extensions(['xml', 'txt]);
        $routes->connect(
            '/sitemap',
            ['plugin' => 'Seo', 'controller' => 'Sitemap', 'action' => 'index'],
            ['_name' => 'seo:sitemap', '_ext' => ['xml']]
        );
        $routes->connect(
            '/sitemap-:sitemap-:page',
            ['plugin' => 'Seo', 'controller' => 'Sitemap', 'action' => 'sitemap'],
            ['pass' => ['sitemap', 'page'], '_ext' => ['xml', 'txt']]
        );
        $routes->connect(
            '/sitemap-:sitemap',
            ['plugin' => 'Seo', 'controller' => 'Sitemap', 'action' => 'sitemap'],
            ['pass' => ['sitemap'], '_ext' => ['xml', 'txt']]
        );

        // stylesheets
        //Router::extensions(['xsl']);
        $routes->connect(
            '/sitemap/style/:name',
            ['plugin' => 'Seo', 'controller' => 'Sitemap', 'action' => 'style'],
            ['pass' => ['name'], '_ext' => ['xsl']]
        );
    }
}
