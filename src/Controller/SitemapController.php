<?php
namespace Seo\Controller;

use Cake\Cache\Cache;
use Cake\Controller\Controller;
use Cake\Core\Configure;
use Cake\Event\Event;
use Cake\Network\Exception\BadRequestException;
use Cake\Network\Exception\NotFoundException;
use Cake\Routing\Router;
use Seo\Sitemap\SitemapLocationsCollector;

/**
 * Class SitemapController
 * @package Seo\Controller
 *
 * @todo Use RequestHandler
 */
class SitemapController extends Controller
{

    /**
     * Sitemap index method
     * Renders a sitemap index xml of available sitemap scopes
     * @TODO If sitemap count == 1, then render that sitemap directly instead of the index view
     */
    public function index()
    {
        $sitemaps = $this->_getSitemaps();
        $indexUrls = [];
        foreach(array_keys($sitemaps) as $sitemap) {
            $indexUrls[] = ['loc' => Router::url(['action' => 'sitemap', $sitemap])];
        }

        $this->viewBuilder()->className('Seo.SitemapXml');

        $this->set('type', 'index');
        $this->set('locations', $indexUrls);
    }

    /**
     * Sitemap view method
     * Renders a list of sitemap locations for given scope in xml format
     *
     * @param null $sitemap
     */
    public function sitemap($sitemap = null)
    {
        if (!$sitemap) {
            throw new BadRequestException();
        }

        $sitemaps = $this->_getSitemaps();
        if (!array_key_exists($sitemap, $sitemaps)) {
            throw new NotFoundException();
        }

        $this->viewBuilder()->className('Seo.SitemapXml');

        $this->set('locations', $sitemaps[$sitemap]);
    }

    protected function _getSitemaps()
    {
        $cacheKey = 'sitemaps';
        $sitemaps = Cache::read($cacheKey);

        if (!$sitemaps) {

            # Collect sitemaps via collector event
            // Example event listener:
            // $this->eventManager()->on('Sitemap.get', function(Event $event) {
            //    $event->subject()->add(new SitemapLocation(['controller' => 'Foo', 'action' => 'bar']));
            // });

            $collector = new SitemapLocationsCollector();
            $event = new Event('Sitemap.get', $collector);
            $this->eventManager()->dispatch($event);

            $sitemaps = $collector->toArray();

            //@TODO Implement sitemap caching
            //Cache::write($cacheKey, $sitemaps);
        }

        return $sitemaps;
    }

}