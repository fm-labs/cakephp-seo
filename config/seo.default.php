<?php
return [
    'Seo' => [
        'Sitemap' => [
            'indexUrl' => '/my-custom-sitemap-url.xml' // URL array or string
        ],
        'Robots' => [
            '*' => [
                '/admin/',
            ]
        ],
        'Google' => [
            'Analytics' => [
                'trackingId' => '' // Google analytics tracking ID
            ]
        ],
    ]
];