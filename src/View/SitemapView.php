<?php
declare(strict_types=1);

namespace Seo\View;

use Cake\View\View;
use Seo\Sitemap\Sitemap;

/**
 * Class SitemapView
 *
 * @package Seo\View
 */
class SitemapView extends View
{
    protected $_responseType = 'xml';

    /**
     * @inheritDoc
     */
    public function initialize(): void
    {
        $ext = $this->getRequest()->getParam('_ext');
        $this->_responseType = $ext === 'txt' ? 'txt' : 'xml';

        $this->setResponse($this->getResponse()
            ->withType($this->_responseType)
            ->withCache(time(), '+1 day'));
    }

    /**
     * @inheritDoc
     */
    public function render(?string $template = null, $layout = null): string
    {
        /** @var \Seo\Sitemap\Sitemap $sitemap */
        $sitemap = $this->get('sitemap', new Sitemap());

        if ($this->_responseType == 'txt') {
            //return $sitemap->toText();
        }

        return $sitemap->toXml();
    }
}
