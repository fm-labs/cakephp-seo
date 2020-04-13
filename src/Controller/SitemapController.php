<?php
declare(strict_types=1);

namespace Seo\Controller;

use Cake\Controller\Controller;
use Cake\Core\Plugin;
use Cake\Http\Exception\BadRequestException;
use Cake\Http\Exception\NotFoundException;
use Seo\Sitemap\Sitemap;
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
     * @TODO If sitemap count == 1, then render that sitemap directly instead of the index view
     */
    public function index()
    {
        $this->viewBuilder()->setClassName('Seo.SitemapXml');

        $indexUrls = function () {
            foreach (Sitemap::configured() as $sitemapId) {
                yield new SitemapUrl(['action' => 'sitemap', '_ext' => 'xml', $sitemapId]);
            }
        };

        $this->set('urls', $indexUrls());
        $this->set('type', 'index');
        $this->set('style', \Cake\Core\Configure::read('Seo.Sitemap.style'));
    }

    /**
     * Sitemap view method
     * Renders a list of sitemap locations for given scope in xml format
     *
     * @param string|null $sitemap Sitemap ID
     * @return void
     * @throws \Exception
     */
    public function sitemap(?string $sitemap = null): void
    {
        $this->viewBuilder()->setClassName('Seo.SitemapXml');

        if (!$sitemap) {
            throw new BadRequestException();
        }

        $urls = Sitemap::getUrls($sitemap);
        $this->set('urls', $urls);
        $this->set('type', 'sitemap');
        $this->set('style', \Cake\Core\Configure::read('Seo.Sitemap.style'));
    }

    /**
     * @param string $name Stylesheet name
     * @return \Cake\Http\Response
     */
    public function style(string $name): \Cake\Http\Response
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
