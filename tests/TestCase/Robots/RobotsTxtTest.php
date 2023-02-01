<?php
declare(strict_types=1);

namespace Seo\Test\TestCase\Robots;

use Cake\TestSuite\TestCase;
use Seo\Lib\Robots\RobotsTxtBuilder;

class RobotsTxtTest extends TestCase
{
    /**
     * @return void
     */
    public function testAddRules(): void
    {
        $rules = [
            '/path1/' => true,
            '/path2/' => false,
        ];

        $robots = new RobotsTxtBuilder();
        $robots->addRules(['*' => $rules]);

        $this->assertEquals($rules, $robots->getRules('*'));
    }

    /**
     * @return void
     */
    public function testAllow(): void
    {
        $robots = new RobotsTxtBuilder();
        $robots->allow('*', '/allow1/');
        $robots->allow('*', ['/allow2/', '/allow3/']);

        $expected = [
            '/allow1/' => RobotsTxtBuilder::ALLOW,
            '/allow2/' => RobotsTxtBuilder::ALLOW,
            '/allow3/' => RobotsTxtBuilder::ALLOW,
        ];
        $this->assertEquals($expected, $robots->getRules('*'));
    }

    /**
     * @return void
     */
    public function testDisallow(): void
    {
        $robots = new RobotsTxtBuilder();
        $robots->disallow('*', '/disallow1/');
        $robots->disallow('*', ['/disallow2/', '/disallow3/']);

        $expected = [
            '/disallow1/' => RobotsTxtBuilder::DISALLOW,
            '/disallow2/' => RobotsTxtBuilder::DISALLOW,
            '/disallow3/' => RobotsTxtBuilder::DISALLOW,
        ];
        $this->assertEquals($expected, $robots->getRules('*'));
    }

    /**
     * @return void
     */
    public function testSetHost(): void
    {
        $robots = new RobotsTxtBuilder();

        $robots->setHost('www.example.org');
        $expected = ['Host: www.example.org', ''];
        $this->assertEquals($expected, $robots->getLines());

        $robots->setHost(null);
        $expected = [];
        $this->assertEquals($expected, $robots->getLines());
    }

    /**
     * @return void
     */
    public function testSetCrawlDelay(): void
    {
        $robots = new RobotsTxtBuilder(['*' => ['/' => true]]);

        $robots->setCrawlDelay(5);
        $expected = ['User-agent: *', 'Crawl-delay: 5', 'Allow: /', ''];
        $this->assertEquals($expected, $robots->getLines());

        $robots->setCrawlDelay(-1);
        $expected = ['User-agent: *', 'Allow: /', ''];
        $this->assertEquals($expected, $robots->getLines());
    }

    /**
     * @return void
     */
    public function testSetSitemap(): void
    {
        $robots = new RobotsTxtBuilder();

        $robots->setSitemap('http://example.org/sitemap.xml');
        $expected = ['Sitemap: http://example.org/sitemap.xml', ''];
        $this->assertEquals($expected, $robots->getLines());

        $robots->setSitemap(null);
        $expected = [];
        $this->assertEquals($expected, $robots->getLines());
    }

    /**
     * @return void
     */
    public function testGetLines(): void
    {
        $robots = new RobotsTxtBuilder();
        $robots->allow('*', '/allow/');
        $robots->disallow('*', '/disallow/');
        $robots->setSitemap('/sitemap.xml');
        $robots->setHost('www.example.com');
        $robots->setCrawlDelay(5);

        $expected = [
            'Sitemap: /sitemap.xml',
            'Host: www.example.com',
            '',
            'User-agent: *',
            'Crawl-delay: 5',
            'Allow: /allow/',
            'Disallow: /disallow/',
            '',
        ];
        $this->assertEquals($expected, $robots->getLines());
    }

    /**
     * @return void
     */
    public function testToString(): void
    {
        $robots = new RobotsTxtBuilder();
        $robots->allow('*', '/allow/');
        $robots->disallow('*', '/disallow/');
        $robots->setSitemap('/sitemap.xml');
        $robots->setHost('www.example.com');
        $robots->setCrawlDelay(5);

        $expected = <<<TEXT
Sitemap: /sitemap.xml
Host: www.example.com

User-agent: *
Crawl-delay: 5
Allow: /allow/
Disallow: /disallow/

TEXT;
        $this->assertTextEquals($expected, $robots->toString());
    }
}
