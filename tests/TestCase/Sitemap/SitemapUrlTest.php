<?php

namespace Seo\Test\TestCase\Sitemap;

use Cake\TestSuite\TestCase;
use Seo\Sitemap\SitemapUrl;

class SitemapUrlTest extends TestCase
{
    /**
     * @return void
     */
    public function testStaticCreateFrom(): void
    {
        $url = SitemapUrl::createFrom('/test');
        $this->assertInstanceOf(SitemapUrl::class, $url);
        $this->assertEquals('/test', $url->loc);

        $url = SitemapUrl::createFrom(new SitemapUrl('/test'));
        $this->assertInstanceOf(SitemapUrl::class, $url);
        $this->assertEquals('/test', $url->loc);
    }

    /**
     * @return void
     */
    public function testSetChangeFreq(): void
    {
        $loc = new SitemapUrl('http://www.example.com');
        $this->assertEquals(null, $loc->changefreq);

        $loc->setChangeFreq('always');
        $this->assertEquals('always', $loc->changefreq);
    }

    /**
     * @return void
     */
    public function testSetLocation(): void
    {
        $url = 'http://www.example.com';
        $loc = new SitemapUrl($url);
        $this->assertEquals($url, $loc->loc);

        $url = 'https://example.org';
        $loc->setLocation($url);
        $this->assertEquals($url, $loc->loc);
    }

    /**
     * @return void
     */
    public function testSetPriority(): void
    {
        $loc = new SitemapUrl('http://www.example.com');
        $this->assertEquals('0.5', $loc->priority);

        $loc->setPriority(0.9);
        $this->assertEquals('0.9', $loc->priority);

        $loc->setPriority(0);
        $this->assertEquals('0', $loc->priority);

        $loc->setPriority(-2);
        $this->assertEquals('0', $loc->priority);

        $loc->setPriority(2);
        $this->assertEquals('1', $loc->priority);
    }

    /**
     * @return void
     * @throws \Exception
     */
    public function testSetLastMod(): void
    {
        $loc = new SitemapUrl('http://www.example.com');
        $this->assertEquals(null, $loc->lastmod);

        $loc->setLastMod('2000-01-01');
        $this->assertEquals('2000-01-01', $loc->lastmod);

        $loc->setLastMod(new \DateTime('1999-01-01'));
        $this->assertEquals('1999-01-01T00:00:00+00:00', $loc->lastmod);
    }

    /**
     * @return void
     */
    public function testIsValid(): void
    {
        $url = new SitemapUrl(null);
        $this->assertFalse($url->isValid());

        $url = new SitemapUrl('/');
        $this->assertTrue($url->isValid());
    }

    /**
     * @return void
     */
    public function testToArray(): void
    {
        $url = new SitemapUrl('/', 0.5, '2000-01-01', 'never');
        $expected = [
            'loc' => '/',
            'priority' => 0.5,
            'lastmod' => '2000-01-01',
            'changefreq' => 'never',
        ];
        $this->assertEquals($expected, $url->toArray());
    }
}
