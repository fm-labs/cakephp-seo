<?php

namespace Seo\Test\TestCase\Sitemap;

use Cake\TestSuite\TestCase;
use Seo\Sitemap\Sitemap;
use Seo\Sitemap\SitemapUrl;
use Seo\Test\App\Sitemap\TestSitemapProvider;

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

    /**
     * @return void
     * @throws \Exception
     */
    public function testStaticGetUrls(): void
    {
        // Sitemap from array
        Sitemap::drop('test');
        Sitemap::setConfig('test', [
            'urls' => $this->urls,
        ]);
        $urls = Sitemap::getProvider('test');
        $this->assertIsIterable($urls);

        // Sitemap from provider (generator class)
        Sitemap::drop('test');
        Sitemap::setConfig('test', [
            'className' => TestSitemapProvider::class,
        ]);
        $urls = Sitemap::getProvider('test');
        $this->assertIsIterable($urls);

        Sitemap::drop('test');

        $this->markTestIncomplete();
    }

    /**
     * @return void
     * @throws \Exception
     */
    public function testToXml(): void
    {
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
        $sitemap = new Sitemap($this->urls);
        $xml = $sitemap->toXml();
        $this->assertTextEquals($expected, $xml);
    }

    /**
     * @return void
     * @throws \Exception
     */
    public function testToXmlWithValidationNs(): void
    {
        //phpcs:disable
        $expected = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd"/>

XML;
        //phpcs:enable
        $sitemap = new Sitemap();
        $sitemap->enableXmlValidationNs(true);
        $xml = $sitemap->toXml();
        $this->assertTextEquals($expected, $xml);
    }

    /**
     * @return void
     * @throws \Exception
     */
    public function testToXmlWithStylesheet(): void
    {
        //phpcs:disable
        // <urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd">
        $expected = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<?xml-stylesheet type="text/xsl" href="/path/to/style.xsl"?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"/>

XML;
        //phpcs:enable
        $sitemap = new Sitemap();
        $sitemap->setStyleUrl('/path/to/style.xsl');
        $xml = $sitemap->toXml();
        $this->assertTextEquals($expected, $xml);
    }

    /**
     * @return void
     * @throws \Exception
     */
    public function testValidateXml(): void
    {
        $sitemap = new Sitemap($this->urls);
        $this->assertTrue($sitemap->validateXml());
    }

    /**
     * @return void
     */
    public function testToLines(): void
    {
        $expected = <<<TEXT
http://www.example.com/
http://www.example.org/

TEXT;

        $sitemap = new Sitemap($this->urls);
        $sitemap->addUrl('http://www.example.org/');
        $this->assertTextEquals($expected, $sitemap->toLines());
    }

    /**
     * @return void
     */
    public function testAddUrl(): void
    {
        $sitemap = new Sitemap();
        $sitemap->addUrl('http://www.example.com/1/');
        $sitemap->addUrl(['loc' => 'http://www.example.com/2/']);
        $sitemap->addUrl(new SitemapUrl('http://www.example.org/3/'));
        $sitemap->addUrl(new SitemapUrl(['loc' => 'http://www.example.org/4/']));

        $expected = <<<TEXT
http://www.example.com/1/
http://www.example.com/2/
http://www.example.org/3/
http://www.example.org/4/

TEXT;
        $this->assertTextEquals($expected, $sitemap->toLines());
    }
}
