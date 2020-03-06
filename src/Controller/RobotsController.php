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
        if (Configure::check('Seo.Robots') && !Configure::read('Seo.Robots.disable')) {
            foreach ((array)Configure::read('Seo.Robots') as $agent => $rules) {
                $lines[] = 'User-agent: ' . $agent;
                foreach ($rules as $location) {
                    try {
                        $lines[] = 'Disallow: ' . Router::url($location, false);
                    } catch (\Exception $ex) {
                    }
                }
                $lines[] = '';
            }
        }

        $this->response->type('text/plain');
        $this->response->body(trim(join("\n", $lines)));

        return $this->response;
    }
}
