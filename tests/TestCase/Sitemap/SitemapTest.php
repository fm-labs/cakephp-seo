<?php

namespace Seo\Test\Sitemap;

use Cake\Core\Configure;
use Cake\TestSuite\TestCase;
use Seo\Sitemap\Provider\ArraySitemapProvider;
use Seo\Sitemap\Sitemap;
use Seo\Test\TestApp\Sitemap\TestSitemapProvider;
use Sitemap\Sitemap\SitemapLocation;

class SitemapTest extends TestCase
{
    public $urls = [
        [
            'loc' => 'http://www.example.com/',
            'lastmod' => '2005-01-01',
            'changefreq' => 'monthly',
            'priority' => 0.8,
        ],
    ];
    
    public $indexUrls = [
        [
            'loc' => 'http://www.example.com/sitemap1.xml.gz',
            'lastmod' => '2004-10-01T18:23:17+00:00',
        ],
        [
            'loc' => 'http://www.example.com/sitemap2.xml.gz',
            'lastmod' => '2005-01-01',
        ],
    ];

    /**
     * @return void
     */
    public function testStaticGetUrls(): void
    {
        // Sitemap from array
        Sitemap::drop('test');
        Sitemap::setConfig('test', [
            'urls' => $this->urls,
        ]);
        $urls = Sitemap::getUrls('test');
        $this->assertIsIterable($urls);

        // Sitemap from provider (generator class)
        Sitemap::drop('test');
        Sitemap::setConfig('test', [
            'className' => TestSitemapProvider::class,
        ]);
        $urls = Sitemap::getUrls('test');
        $this->assertIsIterable($urls);

        Sitemap::drop('test');

        $this->markTestIncomplete();
    }

    /**
     * @return void
     * @throws \Exception
     */
    public function testStaticBuildSitemapXml(): void
    {
        $urls = $this->urls;

        //phpcs:disable
        // <urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd">
        $expected = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
  <url>
    <loc>http://www.example.com/</loc>
    <lastmod>2005-01-01</lastmod>
    <changefreq>monthly</changefreq>
    <priority>0.8</priority>
  </url>
</urlset>

XML;
        //phpcs:enable
        $xml = Sitemap::buildSitemapXml($urls);
        $sitemap = $xml->saveXML();
        $this->assertTextEquals($expected, $sitemap);

        $validate = Sitemap::validateSitemapXml($xml);
        $this->assertTrue($validate);
    }

    /**
     * @return void
     * @throws \Exception
     */
    public function testStaticBuildSitemapIndexXml(): void
    {
        $urls = $this->indexUrls;

        //phpcs:disable
        //<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/siteindex.xsd">
        $expected = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
  <sitemap>
    <loc>http://www.example.com/sitemap1.xml.gz</loc>
    <lastmod>2004-10-01T18:23:17+00:00</lastmod>
  </sitemap>
  <sitemap>
    <loc>http://www.example.com/sitemap2.xml.gz</loc>
    <lastmod>2005-01-01</lastmod>
  </sitemap>
</sitemapindex>

XML;
        //phpcs:enable
        $xml = Sitemap::buildSitemapIndexXml($urls);
        $sitemap = $xml->saveXML();
        $this->assertTextEquals($expected, $sitemap);

        $validate = Sitemap::validateSitemapIndexXml($xml);
        $this->assertTrue($validate);
    }

}
