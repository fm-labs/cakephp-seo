<?php
use Cake\Routing\Router;

// robots.txt
Router::connect('/robots.txt',
    ['plugin' => 'Seo', 'controller' => 'Robots', 'action' => 'index'],
    ['_name' => 'seo:robots']
);

// sitemap.xml
Router::connect('/sitemap.xml',
    ['plugin' => 'Seo', 'controller' => 'Sitemap', 'action' => 'index'],
    ['_name' => 'seo:sitemap']);

// paged sitemaps
Router::connect('/sitemap-:sitemap-:page.xml',
    ['plugin' => 'Seo', 'controller' => 'Sitemap', 'action' => 'sitemap'],
    ['pass' => ['sitemap', 'page']]
);
Router::connect('/sitemap-:sitemap.xml',
    ['plugin' => 'Seo', 'controller' => 'Sitemap', 'action' => 'sitemap'],
    ['pass' => ['sitemap']]
);