<?php
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
    public function initialize()
    {
        $cacheDuration = '+1 day';

        $this->response->type('application/xml');
        $this->response->cache(mktime(0, 0, 0, date('m'), date('d'), date('Y')), $cacheDuration);
    }

    /**
     * @param null $view
     * @param null $layout
     * @return null|string
     */
    public function render($view = null, $layout = null)
    {
        $this->viewPath = 'Sitemap';
        $this->subDir = 'xml';

        return parent::render($view, false);
    }
}
