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
     * Initialize.
     * Set response type and caching
     */
    public function initialize(): void)
    {
        $this->setResponse($this->getResponse()
            ->withType('application/xml')
            ->withCache(mktime(0, 0, 0, date('m'), date('d'), date('Y')), '+1 day'));
    }

    /**
     * @param null $template
     * @param null $layout
     * @return null|string
     */
    public function render(?string $template = null, $layout = null): string
    {
        $this->setTemplatePath('Sitemap');
        $this->setSubDir('xml');

        return parent::render($view, false);
    }
}
