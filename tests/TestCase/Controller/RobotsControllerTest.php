<?php

namespace Seo\Test\TestCase\Controller;

use Cake\Core\Configure;
use Cake\Routing\Router;
use Cake\TestSuite\IntegrationTestCase;

/**
 * Class RobotsControllerTest
 *
 * @package Seo\Test\TestCase\Controller
 */
class RobotsControllerTest extends IntegrationTestCase
{
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

        $this->testConfig = [
            'Seo' => [
                'Sitemap' => [
                    'indexUrl' => '/sitemap-test-url.xml'
                ],
                'Robots' => [
                    '*' => [
                        '/admin/',
                    ],
                    'GoogleBot' => [
                        '/no-google/',
                    ],
                ]
            ]
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
     */
    public function testIndex()
    {
        $this->get('/robots.txt');

        $this->assertResponseOk();
        $this->assertContentType('text/plain; charset=UTF-8');

        $sitemapUrl = Router::url($this->testConfig['Seo']['Sitemap']['indexUrl']);
        $expected = "Sitemap: " .  $sitemapUrl . "\n";
        $this->assertResponseContains($expected);

        $expected = "User-agent: *\nDisallow: /admin/";
        $this->assertResponseContains($expected);

        $response = $this->_response->body();
        $expected = [
            'Sitemap: ' . $sitemapUrl,
            '',
            'User-agent: *',
            'Disallow: /admin/',
            '',
            'User-agent: GoogleBot',
            'Disallow: /no-google/'
        ];
        $this->assertEquals($expected, explode("\n", $response));
    }
}
