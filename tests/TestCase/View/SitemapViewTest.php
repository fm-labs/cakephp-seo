<?php

namespace Seo\Test\TestCase\View;

use Cake\TestSuite\TestCase;
use Seo\Sitemap\Sitemap;
use Seo\Test\TestApp\Sitemap\TestSitemapProvider;
use Seo\View\SitemapView;

class SitemapViewTest extends TestCase
{
    /**
     * @return void
     * @throws \Exception
     */
    public function testRenderXml(): void
    {
        $sitemap = new Sitemap();
        $sitemap->addUrl('/');

        $view = new SitemapView();
        $view->set('sitemap', $sitemap);

        $this->assertEquals('application/xml', $view->getResponse()->getType());
    }

    /**
     * @return void
     * @throws \Exception
     */
    public function testRenderText(): void
    {
        $sitemap = new Sitemap();
        $sitemap->addUrl('/');

        $view = new SitemapView();
        $view->set('sitemap', $sitemap);

        $this->assertEquals('application/xml', $view->getResponse()->getType());
    }
}
