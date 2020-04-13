<?php
return [
    'Seo' => [
        'Sitemap' => [
            'style' => null, // 'pedro' or 'catcto'
            'urls' => [
                [
                    'loc' => 'http://www.example.org',
                    'priority' => 0.5,
                    'lastmod' => '2020-02-02',
                    'changefreq' => 'monthly',
                ],
            ],
        ],
        'Robots' => [
            'sitemapUrl' => null,
            'rules' => [
                '*' => [
                    '/admin/',
                ],
            ],
        ],
        'Google' => [
            'Analytics' => [
                'trackingId' => '',
            ],
        ],
    ],
];
