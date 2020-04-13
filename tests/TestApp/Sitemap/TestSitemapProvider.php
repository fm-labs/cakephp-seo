<?php
namespace Seo\Test\TestApp\Sitemap;

class TestSitemapProvider implements \IteratorAggregate
{
    public $locations = [
        [
            'loc' => 'https://example.org',
            'priority' => 0.5,
            'changefreq' => 'always',
            'lastmod' => '2005-01-01',
        ],
    ];

    /**
     * @inheritDoc
     */
    public function getIterator()
    {
        return array_map(function ($loc) {
            yield new \Seo\Sitemap\SitemapUrl($loc);
        }, $this->locations);
    }
}
