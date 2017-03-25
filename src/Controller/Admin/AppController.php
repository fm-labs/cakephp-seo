<?php

namespace Seo\Controller\Admin;

use App\Controller\Admin\AppController as BaseAdminAppController;

class AppController extends BaseAdminAppController
{
    /**
     * @return array
     * @deprecated Use backend config file instead
     */
    public static function backendMenu()
    {
        return [
            'plugin.seo' => [
                'plugin' => 'Seo',
                'title' => 'Seo',
                'url' => ['plugin' => 'Seo', 'controller' => 'Dashboard', 'action' => 'index'],
                'data-icon' => 'search',

                /*
                '_children' => [
                    'keywords' => [
                        'title' => 'Keywords',
                        'url' => ['plugin' => 'Seo', 'controller' => 'SeoKeywords', 'action' => 'index'],
                        'data-icon' => 'word'
                    ],
                ]
                */
            ]
        ];
    }
}
