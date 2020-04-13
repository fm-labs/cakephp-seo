<?php

namespace Seo\Test\TestCase\Sitemap;

use Cake\TestSuite\TestCase;
use Seo\Sitemap\SitemapUrl;

class SitemapUrlTest extends TestCase
{
    /**
     * @return void
     */
    public function testStaticCreate()
    {
        $this->markTestIncomplete();
    }

    /**
     * @return void
     */
    public function testSetChangeFreq()
    {
        $loc = new SitemapUrl('http://www.example.com');
        $this->assertEquals(null, $loc->changefreq);

        $loc->setChangeFreq('always');
        $this->assertEquals('always', $loc->changefreq);
    }

    /**
     * @return void
     */
    public function testSetLocation()
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
    public function testSetPriority()
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
     */
    public function testSetLastMod()
    {
        $loc = new SitemapUrl('http://www.example.com');
        $this->assertEquals(null, $loc->lastmod);

        $loc->setLastMod('2000-01-01');
        $this->assertEquals('2000-01-01', $loc->lastmod);

        $loc->setLastMod(new \DateTime('1999-01-01'));
        $this->assertEquals('1999-01-01T00:00:00+00:00', $loc->lastmod);

        $loc->setLastMod(new \Cake\I18n\Time('1998-01-01'));
        $this->assertEquals('1998-01-01T00:00:00+00:00', $loc->lastmod);
    }

    /**
     * @return void
     */
    public function testIsValid()
    {
        $this->markTestIncomplete();
    }

    /**
     * @return void
     */
    public function testToArray()
    {
        $this->markTestIncomplete();
    }

}
