<?php
declare(strict_types=1);

namespace Seo\Robots;

use Cake\Routing\Router;

/**
 * Class RobotsTxt
 * @package Seo\Robots
 */
class RobotsTxt
{
    public const ALLOW = true;
    public const DISALLOW = false;

    public const EXTRA_HOST = 'host';
    public const EXTRA_SITEMAP = 'sitemap';
    public const EXTRA_CRAWLDELAY = 'delay';

    /**
     * @var array Extra lines
     */
    protected $_extra = [];

    /**
     * @var array Rules
     */
    protected $_rules = [];

    /**
     * @var array Default rules
     */
    protected $_defaultRules = [
        '*' => [
            '/' => self::ALLOW,
        ],
    ];

    /**
     * RobotsTxt constructor.
     * @param array $rules Initial rules
     */
    public function __construct(array $rules = [])
    {
        $this->setSitemap(null);
        $this->setHost(null);
        $this->setCrawlDelay(-1);
        //$this->setRules($this->_defaultRules);
        $this->addRules($rules);
    }

    /**
     * Set robots rules.
     * @param array $rules Robots rules
     * @return $this
     */
    public function setRules(array $rules)
    {
        $this->_rules = $rules;

        return $this;
    }

    /**
     * Add robots rules.
     * @param array $rules Robots rules [ 'UserAgentString' => ['/allowed' => true, '/not-allowed' => false] ]
     * @return $this
     */
    public function addRules(array $rules)
    {
        foreach ($rules as $ua => $paths) {
            foreach ($paths as $path => $perm) {
                $this->_rules[$ua][$path] = $perm;
            }
        }

        return $this;
    }

    /**
     * @param string $ua User-Agent
     * @param string|array $path List of paths or single path
     * @return $this
     */
    public function disallow(string $ua, $path)
    {
        $path = (array)$path;
        $rules = [];
        foreach ($path as $_path) {
            $rules[$_path] = self::DISALLOW;
        }

        return $this->addRules([$ua => $rules]);
    }

    /**
     * Set allow directive (non-standard)
     * @link https://en.wikipedia.org/wiki/Robots.txt#Allow_directive
     * @param string $ua User-Agent
     * @param string|array $path List of paths or single path
     * @return $this
     */
    public function allow(string $ua, $path)
    {
        $path = (array)$path;
        $rules = [];
        foreach ($path as $_path) {
            $rules[$_path] = self::ALLOW;
        }

        return $this->addRules([$ua => $rules]);
    }

    /**
     * Set non-standard sitemap index URL.
     * @link https://en.wikipedia.org/wiki/Robots.txt#Sitemap
     * @param string|null $url Sitemap URL. Set to NULL to disable.
     * @return $this
     */
    public function setSitemap(?string $url)
    {
        $this->_extra[self::EXTRA_SITEMAP] = $url;

        return $this;
    }

    /**
     * Set non-standard crawl delay directive.
     * @link https://en.wikipedia.org/wiki/Robots.txt#Crawl-delay_directive
     * @param int $delay Host delay. Set to negative value to disable.
     * @return $this
     */
    public function setCrawlDelay(int $delay)
    {
        if ($delay < 0) {
            $delay = null;
        }
        $this->_extra[self::EXTRA_CRAWLDELAY] = $delay;

        return $this;
    }

    /**
     * Set non-standard host directive.
     * @link https://en.wikipedia.org/wiki/Robots.txt#Host
     * @param string|null $host Host domain name. Set to NULL to disable.
     * @return $this
     */
    public function setHost(?string $host)
    {
        $this->_extra[self::EXTRA_HOST] = $host;

        return $this;
    }

    /**
     * Get all rules or by user-agent.
     * @param string|null $ua User-agent string
     * @return array
     */
    public function getRules(?string $ua = null): array
    {
        if ($ua === null) {
            return $this->_rules;
        }

        return $this->_rules[$ua] ?? [];
    }

    /**
     * @return array
     */
    public function getLines(): array
    {
        $lines = [];

        // extra: sitemap
        $sitemap = $this->_extra[self::EXTRA_SITEMAP] ?? null;
        if ($sitemap) {
            $lines[] = sprintf('Sitemap: %s', $sitemap);
        }

        // extra: host
        $host = $this->_extra[self::EXTRA_HOST] ?? null;
        if ($host) {
            $lines[] = sprintf('Host: %s', $host);
        }

        // extra: separator
        if (count($lines) > 0) {
            $lines[] = '';
        }

        // rules
        $rulesAdder = function ($rules) use (&$lines) {
            foreach ($rules as $path => $perm) {
                try {
                    $url = Router::url($path, false);
                    $permWord = $perm == self::ALLOW ? 'Allow' : 'Disallow';
                    $lines[] = sprintf("%s: %s", $permWord, $url);
                } catch (\Exception $ex) {
                }
            }
        };

        foreach ($this->_rules as $ua => $rules) {
            $lines[] = sprintf("User-agent: %s", $ua);
            // crawl delay
            $delay = $this->_extra[self::EXTRA_CRAWLDELAY] ?? null;
            if ($delay) {
                $lines[] = sprintf("Crawl-delay: %d", (int)$delay);
            }

            // allow before disallow
            // https://en.wikipedia.org/wiki/Robots.txt#Allow_directive
            foreach ([self::ALLOW, self::DISALLOW] as $filter) {
                $_rules = array_filter($rules, function ($rule) use ($filter) {
                    return $rule === $filter;
                });

                ksort($rules);
                $rulesAdder($rules);
            }
            $lines[] = '';
        }

        return $lines;
    }

    /**
     * @return string
     */
    public function toString(): string
    {
        return join("\n", $this->getLines());
    }
}
