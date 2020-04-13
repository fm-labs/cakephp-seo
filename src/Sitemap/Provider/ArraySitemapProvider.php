<?php
declare(strict_types=1);

namespace Seo\Sitemap\Provider;

use Seo\Sitemap\SitemapProviderInterface;

class ArraySitemapProvider implements SitemapProviderInterface
{
    protected $urls = [];

    /**
     * ArraySitemapProvider constructor.
     * @param array $config Provider configuration
     */
    public function __construct(array $config)
    {
        $this->urls = $config['urls'] ?? [];
    }

    /**
     * @inheritDoc
     */
    public function getIterator()
    {
        yield from $this->urls;
    }
}
