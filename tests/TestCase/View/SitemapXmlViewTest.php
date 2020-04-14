<?php

namespace Seo\Test\TestCase\View;

use Cake\TestSuite\TestCase;
use Seo\Sitemap\Sitemap;
use Seo\Test\TestApp\Sitemap\TestSitemapProvider;
use Seo\View\SitemapView;

class SitemapXmlViewTest extends TestCase
{
    /**
     * @return void
     * @throws \Exception
     */
    public function testRender(): void
    {
        $this->markTestIncomplete();

        $locations = Sitemap::getProvider('default');
        $view = new SitemapView();
        $view->set('locations', $locations);
        $result = $view->render();
    }
}
