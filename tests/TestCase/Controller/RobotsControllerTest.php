<?php
declare(strict_types=1);

namespace Seo\Test\TestCase\Controller;

use Cake\Core\Configure;
use Cake\Routing\Router;
use Cake\TestSuite\IntegrationTestTrait;
use Cake\TestSuite\TestCase;
use Seo\Test\App\Application;

/**
 * Class RobotsControllerTest
 *
 * @package Seo\Test\TestCase\Controller
 */
class RobotsControllerTest extends TestCase
{
    use IntegrationTestTrait;

    /**
     * {@inheritDoc}
     */
    public function setUp(): void
    {
        parent::setUp();

        Configure::drop('Seo');
        Configure::write('Seo', [
            'Robots' => [
                'sitemapUrl' => '/sitemap-test-url.xml',
                'rules' => [
                    '*' => [
                        '/admin/',
                    ],
                    'GoogleBot' => [
                        '/no-google/',
                    ],
                ],
            ],
        ]);

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
     * Test index method
     * @return void
     * @group integration
     */
    public function testIndex()
    {
        //$this->get('/robots.txt');
        $this->get(['plugin' => 'Seo', 'controller' => 'Robots', 'action' => 'index']);
        $this->assertResponseOk();
        $this->assertContentType('text/plain');

        $sitemapUrl = Router::url(Configure::read('Seo.Robots.sitemapUrl'), true);
        $expected = "Sitemap: " . $sitemapUrl . "\n";
        $this->assertResponseContains($expected);

        $expected = "User-agent: *\nDisallow: /admin/";
        $this->assertResponseContains($expected);

        $response = (string)$this->_response->getBody();
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
