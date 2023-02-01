<?php
declare(strict_types=1);

namespace Seo\Controller;

use Cake\Controller\Controller;
use Cake\Core\Configure;
use Cake\Routing\Router;
use Seo\Lib\Robots\RobotsTxtBuilder;

/**
 * Class RobotsController
 *
 * @package Seo\Controller
 */
class RobotsTxtController extends Controller
{
    protected $defaultRobotRules = [
        '*' => [
            '/user/' => false,
            '/login/' => false,
            '/logout/' => false,
            '/wp-admin/' => false,
            '/administrator/' => false,
            '/adm/' => false,
            '/admin/' => false,
            '/system/' => false,
            '/cache/' => false,
            '/tmp/' => false,
            '/private/' => false,
            '/' => true,
        ],
    ];

    /**
     * Generates robots.txt contents
     *
     * @return \Cake\Http\Response
     * @todo Caching
     */
    public function index()
    {
        $robots = new RobotsTxtBuilder();
        $robots->addRules($this->defaultRobotRules);
        $robots->addRules(Configure::read('Seo.RobotsTxt.rules', []));

        $sitemapUrl = Configure::read('Seo.RobotsTxt.sitemapUrl', ['_name' => 'seo:sitemap', '_ext' => 'xml']);
        $robots->setSitemap(Router::url($sitemapUrl, true));

        return $this->getResponse()
            ->withType('text/plain')
            ->withStringBody($robots->toString());
    }
}
