<?php
declare(strict_types=1);

namespace Seo\Sitemap;

use Cake\Routing\Router;

/**
 * Class SitemapUrl
 *
 * @package Seo\Sitemap
 * @property string $loc
 * @property string $lastmod
 * @property string $changefreq
 * @property string $priority
 */
class SitemapUrl
{
    public const CHANGE_FREQUENCIES = ['always', 'hourly', 'daily', 'weekly', 'monthly', 'yearly', 'never'];

    /**
     * @var array
     */
    protected $_fields = [];

    /**
     * @param string|array|\Seo\Sitemap\SitemapUrl $url Location data
     * @return self
     */
    public static function createFrom($url): self
    {
        if ($url instanceof self) {
            return $url;
        }

        return new self($url);
    }

    /**
     * @param string|array $loc Resource URI
     * @param float $priority Resource priority
     * @param \DateTime|string|null $lastmod Last modified date of resource
     * @param string|null $changefreq Change frequence of resource
     */
    public function __construct($loc, $priority = 0.5, $lastmod = null, $changefreq = null)
    {
        if (is_array($loc) && isset($loc['loc'])) {
            extract($loc, EXTR_IF_EXISTS);
        }

        $this->setLocation($loc);
        $this->setPriority($priority);
        $this->setLastMod($lastmod);
        $this->setChangeFreq($changefreq);
    }

    /**
     * @param string $key Field key
     * @return mixed
     */
    public function __get($key)
    {
        return $this->_fields[$key] ?? null;
    }

    /**
     * @param string|array|null $url Sitemap URL
     * @return $this
     */
    public function setLocation($url)
    {
        if ($url) {
            $url = Router::url($url, true);
        }
        $this->_fields['loc'] = $url;

        return $this;
    }

    /**
     * @param float $priority Sitemap priority. Value between 0 and 1.
     * @return $this
     */
    public function setPriority(float $priority)
    {
        $priority = $priority > 0 ? min($priority, 1) : $priority;
        $priority = $priority < 0 ? max($priority, 0) : $priority;
        $precision = $priority == 0 || $priority == 1 ? 0 : 1;
        $this->_fields['priority'] = number_format($priority, $precision);

        return $this;
    }

    /**
    // @link http://www.w3.org/TR/NOTE-datetime
     * @param \DateTimeInterface|string $lastmod Last modified datetime. W3C time format
     * @return $this
     */
    public function setLastMod($lastmod)
    {
        if (is_object($lastmod)) {
            if ($lastmod instanceof \DateTimeInterface) {
                $lastmod = $lastmod->format(\DateTime::W3C);
            }
        }

        $this->_fields['lastmod'] = (string)$lastmod;

        return $this;
    }

    /**
     * @param string|null $changefreq Resource change frequency.
     *      Possible values: 'always', 'hourly', 'daily', 'weekly', 'monthly', 'yearly', 'never'
     * @return $this
     */
    public function setChangeFreq($changefreq)
    {
        $changefreq = in_array($changefreq, self::CHANGE_FREQUENCIES) ? $changefreq : null;

        $this->_fields['changefreq'] = $changefreq;

        return $this;
    }

    /**
     * @return bool
     */
    public function isValid(): bool
    {
        if (empty($this->_fields['loc'])) {
            return false;
        }

        return true;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return $this->_fields;
    }
}
