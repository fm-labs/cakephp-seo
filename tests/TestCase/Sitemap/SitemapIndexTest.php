<?php

namespace Seo\Test\Sitemap;

use Cake\TestSuite\TestCase;
use Seo\Sitemap\SitemapIndex;

class SitemapIndexTest extends TestCase
{
    public $urls = [
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
     * @throws \Exception
     */
    public function testToXml(): void
    {
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
        $sitemap = new SitemapIndex($this->urls);
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
<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/siteindex.xsd"/>

XML;
        //phpcs:enable
        $sitemap = new SitemapIndex();
        $sitemap->enableXmlValidationNs(true);
        $xml = $sitemap->toXml();
        $this->assertTextEquals($expected, $xml);
    }

    /**
     * @return void
     * @throws \Exception
     */
    public function testValidateXml(): void
    {
        $sitemap = new SitemapIndex($this->urls);
        $this->assertTrue($sitemap->validateXml());
    }
}
