<?php
namespace Seo\View;

use Cake\Network\Exception\NotFoundException;
use Cake\View\View;
use Seo\Controller\Component\SitemapComponent;

class SitemapXmlView extends View
{
    public function initialize()
    {
        $cacheDuration = '+1 day';

        $this->response->type('application/xml');
        $this->response->cache(mktime(0, 0, 0, date('m'), date('d'), date('Y')), $cacheDuration);
    }

    public function render($view = null, $layout = null)
    {
        $this->viewPath = 'Sitemap';
        $this->subDir = 'xml';

        return parent::render($view, false);
    }
}
