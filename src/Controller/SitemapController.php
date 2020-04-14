<?php
declare(strict_types=1);

namespace Seo\Controller;

use Cake\Controller\Controller;
use Cake\Core\Configure;
use Cake\Core\Plugin;
use Cake\Http\Exception\BadRequestException;
use Cake\Http\Exception\NotFoundException;
use Cake\Http\Response;
use Seo\Sitemap\Sitemap;
use Seo\Sitemap\SitemapIndex;
use Seo\Sitemap\SitemapUrl;

/**
 * Class SitemapController
 * @package Seo\Controller
 */
class SitemapController extends Controller
{
    /**
     * Sitemap index method
     * Renders a sitemap index xml of available sitemap scopes
     *
     * @return void
     */
    public function index()
    {
        $this->viewBuilder()->setClassName('Seo.Sitemap');

        $indexUrls = function () {
            foreach (Sitemap::configured() as $sitemapId) {
                yield new SitemapUrl(['action' => 'sitemap', '_ext' => 'xml', $sitemapId]);
            }
        };

        $sitemap = new SitemapIndex();
        $sitemap->addProvider($indexUrls);
        $sitemap->setStyleUrl(Configure::read('Seo.Sitemap.styleUrl'));

        $this->set('sitemap', $sitemap);
    }

    /**
     * Sitemap view method
     * Renders a list of sitemap locations for given scope in xml format
     *
     * @param string|null $scope Sitemap scope
     * @return void
     * @throws \Exception
     */
    public function sitemap(?string $scope = null): void
    {
        $this->viewBuilder()->setClassName('Seo.Sitemap');

        if (!$scope) {
            throw new BadRequestException();
        }

        $provider = Sitemap::getProvider($scope);

        $sitemap = new Sitemap();
        $sitemap->addProvider($provider);

        $this->set('sitemap', $sitemap);
        $this->set('styleUrl', Configure::read('Seo.Sitemap.styleUrl'));
    }

    /**
     * @param string $name Stylesheet name
     * @return \Cake\Http\Response
     */
    public function style(string $name): Response
    {
        $file = Plugin::path('Seo') . 'resources' . DS . 'stylesheet' . DS . 'sitemap-' . $name . '.xsl';
        if (!file_exists($file)) {
            throw new NotFoundException();
        }

        $this->getResponse()
            ->setTypeMap('xsl', 'text/xsl');

        return $this->getResponse()
            ->withType('text/xsl')
            //->withCache('+1 day')
            ->withFile($file);
    }
}
