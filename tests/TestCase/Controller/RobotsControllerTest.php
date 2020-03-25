<?php

namespace Seo\Test\TestCase\Controller;

use Cake\Core\Configure;
use Cake\Routing\Router;
use Cake\TestSuite\IntegrationTestTrait;
use Cake\TestSuite\TestCase;

/**
 * Class RobotsControllerTest
 *
 * @package Seo\Test\TestCase\Controller
 */
class RobotsControllerTest extends TestCase
{
    use IntegrationTestTrait;

    /**
     * @var array
     */
    public $testConfig;

    /**
     * {@inheritDoc}
     */
    public function setUp()
    {
        parent::setUp();

        //$this->useHttpServer(true);
        $this->testConfig = [
            'Seo' => [
                'Sitemap' => [
                    'indexUrl' => '/sitemap-test-url.xml',
                ],
                'Robots' => [
                    '*' => [
                        '/admin/',
                    ],
                    'GoogleBot' => [
                        '/no-google/',
                    ],
                ],
            ],
        ];
        Configure::write($this->testConfig);
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
     * Test index method
     * @return void
     */
    public function testIndex()
    {
        //$this->get('/robots.txt');
        $this->get(['plugin' => 'Seo', 'controller' => 'Robots', 'action' => 'index']);

        $this->assertResponseOk();
        $this->assertContentType('text/plain');

        $sitemapUrl = Router::url($this->testConfig['Seo']['Sitemap']['indexUrl']);
        $expected = "Sitemap: " . $sitemapUrl . "\n";
        $this->assertResponseContains($expected);

        $expected = "User-agent: *\nDisallow: /admin/";
        $this->assertResponseContains($expected);

        $response = $this->_response->getBody();
        $expected = [
            'Sitemap: ' . $sitemapUrl,
            '',
            'User-agent: *',
            'Disallow: /admin/',
            '',
            'User-agent: GoogleBot',
            'Disallow: /no-google/',
        ];
        $this->assertEquals($expected, explode("\n", $response));
    }
}
