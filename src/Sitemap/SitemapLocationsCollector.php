<?php

namespace Seo\Sitemap;

use Cake\Collection\Collection;
use Seo\Sitemap\SitemapLocation;

/**
 * Class SitemapLocationsCollector
 *
 * Simple collector of SitemapLocation objects
 *
 * @package Seo\Sitemap
 */
class SitemapLocationsCollector
{
    /**
     * @var array Scoped list of SitemapLocation objects
     */
    protected $_locations = [];

    /**
     * @param \Seo\Sitemap\SitemapLocation|array $loc
     * @param string $scope Sitemap scope. Defaults to 'default'.
     * @return $this
     */
    public function add($loc, $scope = 'default')
    {
        if (is_array($loc)) {
            foreach ($loc as $_loc) {
                $this->add($_loc, $scope);
            }

            return $this;
        }

        if (!array_key_exists($scope, $this->_locations)) {
            $this->_locations[$scope] = [];
        }

        array_push($this->_locations[$scope], $loc);

        return $this;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return $this->_locations;
    }

    /**
     * Unused
     * @return Collection
     */
    public function toCollections()
    {
        $col = [];
        foreach ($this->_locations as $scope => $locations) {
            $col[$scope] = new Collection($locations);
        }

        return $col;
    }
}
