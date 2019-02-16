<?php
use Cake\Routing\Router;

//Router::extensions(['xml']);
// robots.txt
Router::connect(
    '/robots.txt',
    ['plugin' => 'Seo', 'controller' => 'Robots', 'action' => 'index'],
    ['_name' => 'seo:robots']
);

// sitemap.xml
Router::connect(
    '/sitemap',
    ['plugin' => 'Seo', 'controller' => 'Sitemap', 'action' => 'index'],
    ['_name' => 'seo:sitemap', '_ext' => ['xml']]
);

// paged sitemaps
Router::connect(
    '/sitemap-:sitemap-:page',
    ['plugin' => 'Seo', 'controller' => 'Sitemap', 'action' => 'sitemap'],
    ['pass' => ['sitemap', 'page'], '_ext' => ['xml']]
);
Router::connect(
    '/sitemap-:sitemap',
    ['plugin' => 'Seo', 'controller' => 'Sitemap', 'action' => 'sitemap'],
    ['pass' => ['sitemap'], '_ext' => ['xml']]
);
