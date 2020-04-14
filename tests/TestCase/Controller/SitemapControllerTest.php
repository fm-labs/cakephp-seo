<?php
declare(strict_types=1);

namespace Seo\Test\TestCase\Controller;

use Cake\Core\Configure;
use Cake\Routing\Router;
use Cake\TestSuite\IntegrationTestTrait;
use Cake\TestSuite\TestCase;
use Seo\Sitemap\SitemapUrl;
use Seo\Test\App\Application;

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
    }

    /**
     * {@inheritDoc}
     */
    public function tearDown(): void
    {
        parent::tearDown();

        Configure::delete('Seo');
        unset($this->testConfig);
    }

    /**
     * Test index action
     * @return void
     * @group integration
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
}
