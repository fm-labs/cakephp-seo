<?php

namespace Seo\Controller;

use Cake\Controller\Controller;
use Cake\Core\Configure;
use Cake\Routing\Router;

/**
 * Class RobotsController
 *
 * @package Seo\Controller
 */
class RobotsController extends Controller
{
    protected $defaultRobotRules = [
        '*' => [
            '/user/',
            '/login/',
            '/logout/',
            '/wp-admin/',
            '/administrator/',
            '/adm/',
            '/admin/',
            '/system/',
            '/cache/',
            '/tmp/',
            '/private/'
        ]
    ];

    /**
     * Generates robots.txt in webroot
     *
     * @return \Cake\Http\Response
     * @todo Caching
     * @todo Collect user agent rules via event
     */
    public function index()
    {
        $this->autoRender = false;

        // sitemap
        $lines = [];
        if (!Configure::read('Seo.Sitemap.disable') && !Configure::read('Seo.Robots.disable')) {
            $sitemapUrl = Configure::read('Seo.Sitemap.indexUrl');
            $sitemapUrl = ($sitemapUrl) ?: ['_name' => 'seo:sitemap', '_ext' => 'xml'];

            $lines[] = 'Sitemap: ' . Router::url($sitemapUrl, true);
            $lines[] = '';
        }

        // user agent rules
        $rules = (Configure::check('Seo.Robots')) ? Configure::read('Seo.Robots') : $this->defaultRobotRules;
        if ($rules && !isset($rules['disabled'])) {
            foreach ($rules as $agent => $_rules) {
                $lines[] = 'User-agent: ' . $agent;
                foreach ($_rules as $location) {
                    try {
                        $lines[] = 'Disallow: ' . Router::url($location, false);
                    } catch (\Exception $ex) {
                    }
                }
                $lines[] = '';
            }
        }

        return $this->getResponse()
            ->withType('text/plain')
            ->withStringBody(trim(join("\n", $lines)));
    }
}
