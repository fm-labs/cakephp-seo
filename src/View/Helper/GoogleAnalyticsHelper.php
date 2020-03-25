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
     * @param \Cake\Event\Event $event
     */
    public function beforeLayout(Event $event)
    {
        $trackingId = Configure::read('Seo.Google.Analytics.trackingId');
        $disabled = Configure::read('Seo.Tracking.disabled') || $this->_View->get('_no_tracking') || $this->_View->get('_private');

        $html = "";
        if ($trackingId && !$disabled) {
            $html = $this->_View->element('Seo.Tracking/google_' . $this->getConfig('implementation'), ['trackingId' => $trackingId]);

            // disable in debug mode
            if (Configure::read('debug')) {
                $html = "<!-- " . $html . " -->";
                $html .= "<script>console.log('Seo: Google analytics tracking script has been disabled in debug mode')</script>";
            }
        }

        $this->_View->assign($this->getConfig('block'), $html);
    }
}
