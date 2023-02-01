<?php
declare(strict_types=1);

namespace Seo\Controller;

use Cake\Controller\Controller;
use Cake\Core\Configure;
use Cake\Core\Plugin;
use Cake\Event\Event;
use Cake\Http\Exception\InternalErrorException;
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

        $indexUrlProvider = function () {
            foreach (Sitemap::configured() as $sitemapId) {
                yield new SitemapUrl(['action' => 'sitemap', '_ext' => 'xml', $sitemapId], 0.5);
            }
        };

        $sitemap = new SitemapIndex();
        $sitemap->addProvider($indexUrlProvider);
        $sitemap->setStyleUrl(Configure::read('Seo.Sitemap.styleUrl'));

        $this->getEventManager()->dispatch(new Event('Seo.Sitemap.buildIndex', $sitemap));

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

        if (!$scope || !Sitemap::getConfig($scope)) {
            throw new NotFoundException();
        }

        try {
            $sitemap = new Sitemap();
            $sitemap->setStyleUrl(Configure::read('Seo.Sitemap.styleUrl'));

            $provider = Sitemap::getProvider($scope);
            $sitemap->addProvider($provider);

            $this->getEventManager()->dispatch(new Event('Seo.Sitemap.build', $sitemap));

            $this->set('sitemap', $sitemap);
        } catch (\Exception $ex) {
            throw new InternalErrorException($ex->getMessage());
        }
    }

    /**
     * @param string $name Stylesheet name
     * @return \Cake\Http\Response
     */
    public function style(string $name): Response
    {
        $this->getResponse()->setTypeMap('xsl', 'text/xsl');
        $this->setResponse($this->getResponse()
            ->withType('text/xsl'));

        $file = Plugin::path('Seo') . 'webroot' . DS . 'xsl' . DS . 'sitemap-' . $name . '.xsl';
        if (!file_exists($file)) {
            throw new NotFoundException();
        }

        return $this->getResponse()
            //->withCache('+1 day')
            ->withFile($file);
    }
}
