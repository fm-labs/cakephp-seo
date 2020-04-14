<?php
declare(strict_types=1);

namespace Seo\Sitemap;

use Cake\Core\App;
use Cake\Core\Plugin;
use Cake\Core\StaticConfigTrait;

/**
 * Class Sitemap
 *
 * @package Seo\Sitemap
 */
class Sitemap implements \IteratorAggregate
{
    use StaticConfigTrait;

    public const XML_NS = "http://www.sitemaps.org/schemas/sitemap/0.9";
    public const XML_NS_XSI = "http://www.w3.org/2001/XMLSchema-instance";
    public const XML_SCHEMA_SITEMAP = "http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd";
    public const XML_SCHEMA_INDEX = "http://www.sitemaps.org/schemas/sitemap/0.9/siteindex.xsd";

    public const STYLES = ['pedro', 'catcto'];

    /**
     * @var array|\Seo\Sitemap\SitemapUrl[] List of registered sitemap urls
     */
    protected $urls = [];

    /**
     * @var array List of attached providers
     */
    protected $providers = [];

    /**
     * @var \DOMDocument Sitemap XML document
     */
    protected $xml;

    /**
     * @var string Path to XML schema validation file
     */
    protected $xmlValidationFile;

    /**
     * @var array Sitemap options
     */
    protected $options = ['stylesheet' => null, 'validationNs' => null];

    /**
     * Get a sitemap generator instance.
     *
     * @param string $name Sitemap name
     * @return array|\Traversable
     * @throws \Exception
     */
    public static function getProvider(string $name)
    {
        $config = self::getConfigOrFail($name);
        if (isset($config['urls'])) {
            return $config['urls'];
        }

        $className = $config['className'] ?? null;

        if (is_string($className)) {
            $className = App::className($className, 'Sitemap', 'Provider');
            $className = new $className($config);
        }

        if (!($className instanceof \Traversable)) {
            throw new \Exception(
                sprintf("Sitemap provider for '%s' is not a valid generator", $name)
            );
        }

        return $className;
    }

    /**
     * Sitemap constructor.
     * @param array|\Seo\Sitemap\SitemapUrl[] $urls List of sitemap urls
     * @param array $options Sitemap options
     */
    public function __construct(array $urls = [], array $options = [])
    {
        $this->options += $options;
        $this->urls = $urls;
        $this->xml = null;
        $this->xmlValidationFile = Plugin::path('Seo') . DS . 'resources' . DS . 'schema' . DS . 'sitemap.xsd';

        $this->addProvider($this);
    }

    /**
     * Add a sitemap url provider.
     * @param array|callable|\Traversable $provider Sitemap url provider
     * @return $this
     */
    public function addProvider($provider)
    {
        $this->providers[] = $provider;
        $this->xml = null;

        return $this;
    }

    /**
     * Add a single sitemap url.
     * @param string|array|\Seo\Sitemap\SitemapUrl $url Sitemap url
     * @return $this
     */
    public function addUrl($url)
    {
        $this->urls[] = $url;
        $this->xml = null;

        return $this;
    }

    /**
     * @param null|string|array $url Sitemap XSL stylesheet URL
     * @return $this
     */
    public function setStyleUrl($url = null)
    {
        $this->options['stylesheet'] = $url;
        $this->xml = null;

        return $this;
    }

    /**
     * Enable the XML validation namespace declaration in the sitemap XML root element
     * @param bool $enable Enable flag
     * @return $this
     */
    public function enableXmlValidationNs(bool $enable)
    {
        $this->options['validationNs'] = $enable;
        $this->xml = null;

        return $this;
    }

    /**
     * Returns sitemap urls from the local sitemap url list as a \Traversable.
     * @return \Traversable
     */
    public function getIterator(): \Traversable
    {
        return new \ArrayIterator($this->urls);
    }

    /**
     * Returns sitemap urls from all attached providers as a \Traversable.
     * @return \Traversable
     * @throws \Exception
     */
    protected function getUrls(): \Traversable
    {
        foreach ($this->providers as $provider) {
            if ($provider instanceof \Closure || is_callable($provider)) {
                yield from $provider();
            } elseif (is_array($provider)) {
                yield from $provider;
            } elseif ($provider instanceof \Traversable) {
                yield from $provider;
            } elseif ($provider instanceof \IteratorAggregate) {
                yield from $provider->getIterator();
            }
        }
    }

    /**
     * The sitemap XML document.
     * @return \DOMDocument
     * @throws \Exception
     */
    public function xml(): \DOMDocument
    {
        if ($this->xml === null) {
            $this->xml = $this->buildXml();
        }

        return $this->xml;
    }

    /**
     * Create new sitemap xml DOMDocument
     *
     * @param array $options Sitemap xml options
     * @return \DOMDocument
     */
    protected function initXml(array $options = []): \DOMDocument
    {
        $options += ['stylesheet' => null];

        /* create a dom document with encoding utf8 */
        $doc = new \DOMDocument('1.0', 'UTF-8');
        $doc->formatOutput = true;
        $doc->preserveWhiteSpace = false;

        if ($options['stylesheet']) {
            $styleUrl = strpos($options['stylesheet'], "/") === false
                        && in_array($options['stylesheet'], self::STYLES)
                ? '/sitemap/style/' . $options['stylesheet']
                : $options['stylesheet'];

            $xslt = $doc->createProcessingInstruction(
                'xml-stylesheet',
                'type="text/xsl" href="' . $styleUrl . '"'
            );
            $doc->appendChild($xslt);
        }

        return $doc;
    }

    /**
     * @return \DOMDocument
     * @throws \Exception
     */
    protected function buildXml(): \DOMDocument
    {
        $options = $this->options;
        $doc = $this->initXml($options);

        /* create the root element of the xml tree */
        $xmlRoot = $doc->createElement("urlset");
        /* set the namespace attribute */
        $xmlRoot->setAttribute('xmlns', self::XML_NS);
        /* set the required attributes for validation */
        if ($options['validationNs'] === true) {
            $xmlRoot->setAttribute('xmlns:xsi', self::XML_NS_XSI);
            $xmlRoot->setAttribute('xsi:schemaLocation', self::XML_NS . " " . self::XML_SCHEMA_SITEMAP);
        }
        /* append it to the document created */
        $xmlRoot = $doc->appendChild($xmlRoot);

        /* create element for each location and append to root element */
        foreach ($this->getUrls() as $url) {
            $url = SitemapUrl::createFrom($url);
            if (!$url->isValid()) {
                continue;
            }

            $element = $xmlRoot->appendChild($doc->createElement("url"));
            $element->appendChild($doc->createElement('loc', $url->loc));
            if ($url->lastmod) {
                $element->appendChild($doc->createElement('lastmod', $url->lastmod));
            }
            if ($url->changefreq) {
                $element->appendChild($doc->createElement('changefreq', $url->changefreq));
            }
            if ($url->priority) {
                $element->appendChild($doc->createElement('priority', $url->priority));
            }
        }

        return $doc;
    }

    /**
     * Validate sitemap xml
     *
     * @return bool
     * @throws \Exception
     */
    public function validateXml(): bool
    {
        return $this->validateXmlDocument(
            $this->xml(),
            $this->xmlValidationFile
        );
    }

    /**
     * Validate xml document against schema
     *
     * @param \DOMDocument $xml XML document
     * @param string $schemaFile Path to schema file
     * @return bool
     */
    protected function validateXmlDocument(\DOMDocument $xml, string $schemaFile): bool
    {
        // bug: validation does not work with manually created xml documents
        // workaround: save the xml as string and create new document from xml string and then validate
        $xmlString = $xml->saveXML();
        $tmp = new \DOMDocument();
        $tmp->loadXML($xmlString);

        return $tmp->schemaValidate($schemaFile);
    }

    /**
     * Returns XML string
     * @return string
     * @throws \Exception
     */
    public function toXml(): string
    {
        return $this->xml()->saveXML();
    }

    /**
     * Returns the text representation string (One line per sitemap url entry)
     * @return string
     * @throws \Exception
     */
    public function toText(): string
    {
        $lines = "";
        foreach ($this->getUrls() as $url) {
            $url = SitemapUrl::createFrom($url);
            if ($url->isValid()) {
                $lines .= $url->loc . "\n";
            }
        }

        return $lines;
    }
}
