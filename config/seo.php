<?php
return [
    'Seo' => [
        'RobotsTxt' => [
            'sitemapUrl' => null,
            'rules' => [
                '*' => [
                    '/admin/',
                ],
            ],
        ],
        'Sitemap' => [
            'styleUrl' => 'catcto', // 'pedro' or 'catcto'
            'providers' => []
        ],
        'Google' => [
            'Analytics' => [
                'trackingId' => null,
            ],
        ],
    ],
];
