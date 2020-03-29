<?php
declare(strict_types=1);

namespace Seo\View;

use Cake\View\View;

/**
 * Class SitemapXmlView
 *
 * @package Seo\View
 */
class SitemapXmlView extends View
{
    /**
     * {@inheritDoc}
     */
    public function initialize(): void
    {
        $this->setResponse($this->getResponse()
            ->withType('application/xml')
            ->withCache(time(), '+1 day'));
    }

    /**
     * {@inheritDoc}
     */
    public function render(?string $template = null, $layout = null): string
    {
        $this->setTemplatePath('Sitemap');
        $this->setSubDir('xml');

        return parent::render($template, false);
    }
}
