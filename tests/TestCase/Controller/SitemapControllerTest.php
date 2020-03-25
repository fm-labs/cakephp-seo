<?php

namespace Seo\Test\TestCase\Controller;

use Cake\Core\Configure;
use Cake\Event\Event;
use Cake\Event\EventList;
use Cake\Event\EventManager;
use Cake\Routing\Router;
use Cake\TestSuite\IntegrationTestTrait;
use Cake\TestSuite\TestCase;
use Seo\Sitemap\SitemapLocation;

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
    public function setUp()
    {
        parent::setUp();
        EventManager::instance()->setEventList(new EventList());
        EventManager::instance()->addEventToList(new Event('Sitemap.get'));
        EventManager::instance()->on('Sitemap.get', function (Event $event) {
            $event->getSubject()->add(new SitemapLocation('/foo', 0.8, time(), 'monthly'), 'test');
        });
    }

    /**
     * {@inheritDoc}
     */
    public function tearDown()
    {
        parent::tearDown();

        Configure::delete('Seo');
        unset($this->testConfig);
    }

    /**
     * Test index action
     * @return void
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
