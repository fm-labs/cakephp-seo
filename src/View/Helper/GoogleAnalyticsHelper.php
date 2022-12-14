<?php
declare(strict_types=1);

namespace Seo\View\Helper;

use Cake\Core\Configure;
use Cake\Event\Event;
use Cake\View\Helper;

/**
 * Class GoogleAnalyticsHelper
 *
 * @package Seo\View\Helper
 */
class GoogleAnalyticsHelper extends Helper
{
    protected $_defaultConfig = [
        'implementation' => 'gtag', // 'gtag' or 'analytics'
        'block' => 'google_analytics',
    ];

    /**
     * @param \Cake\Event\Event $event Event object
     * @return void
     */
    public function beforeLayout(Event $event): void
    {
        /** @var \Cake\View\View $view */
        $view = $event->getSubject();

        $trackingId = Configure::read('Seo.Google.Analytics.trackingId');
        $disabled = Configure::read('Seo.Tracking.disabled')
            || $view->get('_no_tracking')
            || $view->get('_private');

        if ($trackingId && !$disabled) {
            $html = $view->element(
                'Seo.Tracking/google_' . $this->getConfig('implementation'),
                ['trackingId' => $trackingId]
            );

            // disable in debug mode
            if (Configure::read('debug')) {
                $devMsg = "Seo: Google analytics tracking script has been disabled in debug mode";
                $html = "<!-- " . $html . " -->";
                $html .= sprintf("<script>console.log('%s')</script>", $devMsg);
            }

            $event->getSubject()->assign($this->getConfig('block'), $html);
        }
    }
}
