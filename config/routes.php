<?php
use Cake\Routing\Router;

// robots.txt
Router::extensions(['xml']);
Router::connect(
    '/robots.txt',
    ['plugin' => 'Seo', 'controller' => 'Robots', 'action' => 'index'],
    ['_name' => 'seo:robots']
);

// sitemap.xml
Router::connect(
    '/sitemap',
    ['plugin' => 'Seo', 'controller' => 'Sitemap', 'action' => 'index'],
    ['_name' => 'seo:sitemap']
);

// paged sitemaps
Router::connect(
    '/sitemap-:sitemap-:page',
    ['plugin' => 'Seo', 'controller' => 'Sitemap', 'action' => 'sitemap'],
    ['pass' => ['sitemap', 'page']]
);
Router::connect(
    '/sitemap-:sitemap',
    ['plugin' => 'Seo', 'controller' => 'Sitemap', 'action' => 'sitemap'],
    ['pass' => ['sitemap']]
);
