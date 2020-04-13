<?php

namespace Seo\Test\View;

use Cake\TestSuite\TestCase;
use Seo\Sitemap\Sitemap;
use Seo\Test\TestApp\Sitemap\TestSitemapProvider;
use Seo\View\SitemapXmlView;

class SitemapXmlViewTest extends TestCase
{
    /**
     * @return void
     * @throws \Exception
     */
    public function testRender(): void
    {
        $this->markTestIncomplete();

        $locations = Sitemap::getUrls('default');
        $view = new SitemapXmlView();
        $view->set('locations', $locations);
        $result = $view->render();
    }
}
