<?php
declare(strict_types=1);

namespace Seo\Test\TestCase\Controller;

use Cake\Routing\Router;
use Cake\TestSuite\IntegrationTestTrait;
use Cake\TestSuite\TestCase;
use Seo\Sitemap\Sitemap;
use Seo\Test\App\Application;
use Seo\Test\TestCase\Sitemap\SitemapTest;

/**
 * Class SitemapControllerTest
 *
 * @package Seo\Test\TestCase\Controller
 */
class SitemapControllerTest extends TestCase
{
    use IntegrationTestTrait;

    /**
     * {@inheritDoc}
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->configApplication(Application::class, []);

        Sitemap::drop('test');
        Sitemap::setConfig('test', [
            'urls' => SitemapTest::SITEMAP_URLS,
        ]);
    }

    /**
     * {@inheritDoc}
     */
    public function tearDown(): void
    {
        parent::tearDown();
    }

    /**
     * Test index action
     * @return void
     * @group integration
     * @throws \Throwable
     */
    public function testIndex()
    {
        //$this->get('/sitemap.xml');
        $this->get(['plugin' => 'Seo', 'controller' => 'Sitemap', 'action' => 'index']);

        $this->assertResponseOk();
        $this->assertContentType('application/xml');

        $expectedSitemapUrl = Router::url('/sitemap-test.xml', true);
        $expectedXml = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
	<sitemap>
		<loc>%s</loc>
	</sitemap>
</sitemapindex>
XML;
        $expectedXml = sprintf($expectedXml, $expectedSitemapUrl);
        $this->assertXmlStringEqualsXmlString($expectedXml, (string)$this->_response->getBody());
    }

    /**
     * @return void
     * @throws \Throwable
     */
    public function testSitemap(): void
    {
        $this->get(['plugin' => 'Seo', 'controller' => 'Sitemap', 'action' => 'sitemap', 'test']);

        $this->assertResponseOk();
        $this->assertContentType('application/xml');

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
        $this->assertXmlStringEqualsXmlString($expected, (string)$this->_response->getBody());
    }

    /**
     * @return void
     * @throws \Throwable
     */
    public function testStyle(): void
    {
        $this->get(['plugin' => 'Seo', 'controller' => 'Sitemap', 'action' => 'style', 'pedro']);

        $this->assertResponseOk();
        $this->assertContentType('text/xsl');
    }

    /**
     * @return void
     * @throws \Throwable
     */
    public function testSitemapTxt(): void
    {
        //$this->get('/sitemap.txt');
        $this->get(['plugin' => 'Seo', 'controller' => 'Sitemap', 'action' => 'sitemap', 'test', '_ext' => 'txt']);

        $this->assertResponseOk();
        $this->assertContentType('text/plain');
    }
}
