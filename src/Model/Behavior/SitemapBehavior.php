<?php

namespace Seo\Model\Behavior;

use Cake\Collection\Collection;
use Cake\Datasource\ResultSetInterface;
use Cake\ORM\Behavior;
use Cake\ORM\Query;
use Seo\Sitemap\SitemapLocation;

/**
 * Class SitemapBehavior
 *
 * @package Seo\Model\Behavior
 */
class SitemapBehavior extends Behavior
{

    protected $_defaultConfig = [
        'fields' => [
            'loc'           => 'sitemap_loc',       // fallback to url
            'priority'      => 'sitemap_priority',  // fallback to default value
            'lastmod'       => 'sitemap_lastmod',   // fallback to modified
            'changefreq'    => 'sitemap_changefreq' // fallback to default value
        ],
        'implementedMethods' => [
        ],
        'implementedFinders' => [
            'sitemap' => 'findSitemap'
        ]
    ];

    /**
     * Auto-wire models
     *
     * @param array $config
     * @throws \Exception
     */
    public function initialize(array $config)
    {
    }

    /**
     * @param Query $query
     * @param array $options
     * @return Query
     */
    public function findSitemap(Query $query, array $options = [])
    {
        if (method_exists($this->_table, 'findSitemap')) {
            return call_user_func([$this->_table, 'findSitemap'], $query);
        }

        return $this->formatSitemap($query, $options);
    }


    /**
     * Formats query as a flat list where the keys are the primary key for the table
     * and the values are the display field for the table. Values are prefixed to visually
     * indicate relative depth in the tree.
     *
     * ### Options
     *
     * - keyPath: A dot separated path to the field that will be the result array key, or a closure to
     *   return the key from the provided row.
     * - valuePath: A dot separated path to the field that is the array's value, or a closure to
     *   return the value from the provided row.
     * - spacer: A string to be used as prefix for denoting the depth in the tree for each item.
     *
     * @param \Cake\ORM\Query $query The query object to format.
     * @param array $options Array of options as described above.
     * @return \Cake\ORM\Query Augmented query.
     */
    public function formatSitemap(Query $query, array $options = [])
    {
        return $query->formatResults(function (ResultSetInterface $results) use ($options) {

            $locations = [];
            $fields = $this->config('fields');
            foreach ($results as $entity) {

                try {

                    $location = $entity[$fields['loc']];
                    $location = ($location) ?: $entity['url'];
                    if (!$location) {
                        continue;
                    }

                    $priority = $entity[$fields['priority']];

                    $lastmod = $entity[$fields['lastmod']];
                    $lastmod = ($lastmod) ?: $entity['modified'];
                    $lastmod = ($lastmod) ?: $entity['updated'];

                    $frequency = $entity[$fields['changefreq']];

                    $loc = new SitemapLocation($location, $priority, $lastmod, $frequency);
                    $locations[$loc->loc] = $loc;

                } catch (\Exception $ex) {}
            }

            return new Collection($locations);
        });
    }

}