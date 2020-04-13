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
class Sitemap
{
    use StaticConfigTrait;

    public const XML_NS = "http://www.sitemaps.org/schemas/sitemap/0.9";
    //public const XML_NS_XSI = "http://www.w3.org/2001/XMLSchema-instance";
    //public const XML_SCHEMA_SITEMAP = "http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd";
    //public const XML_SCHEMA_INDEX = "http://www.sitemaps.org/schemas/sitemap/0.9/siteindex.xsd";

    /**
     * Get a sitemap generator instance.
     *
     * @param string $name Sitemap name
     * @return array|\Traversable
     * @throws \Exception
     */
    public static function getUrls(string $name)
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

        if ($className instanceof \IteratorAggregate) {
            return $className->getIterator();
        }

        if ($className instanceof \Traversable) {
            return $className;
        }

        throw new \Exception(
            sprintf("Sitemap provider for '%s' is not a valid generator", $name)
        );
    }

    /**
     * Create new sitemap xml DOMDocument
     *
     * @param array $options Sitemap xml options
     * @return \DOMDocument
     */
    protected static function initXml(array $options = []): \DOMDocument
    {
        $options += ['style' => null];
        if ($options['style'] && !in_array($options['style'], ['pedro', 'catcto'])) {
            $options['style'] = null;
        }

        /* create a dom document with encoding utf8 */
        $doc = new \DOMDocument('1.0', 'UTF-8');
        $doc->formatOutput = true;
        $doc->preserveWhiteSpace = false;

        if ($options['style']) {
            $styleUrl = '/sitemap/style/' . $options['style'];
            $xslt = $doc->createProcessingInstruction(
                'xml-stylesheet',
                'type="text/xsl" href="' . $styleUrl . '"'
            );
            $doc->appendChild($xslt);
        }

        return $doc;
    }

    /**
     * @param \Seo\Sitemap\SitemapUrl[]|\Generator $urls Sitemap locations
     * @param array $options Options
     * @return \DOMDocument
     */
    public static function buildSitemapXml($urls, array $options = []): \DOMDocument
    {
        $doc = static::initXml($options);

        /* create the root element of the xml tree */
        $xmlRoot = $doc->createElement("urlset");
        /* set the namespace attribute */
        $xmlRoot->setAttribute('xmlns', self::XML_NS);
        /* set the required attributes for validation */
        //$xmlRoot->setAttribute('xmlns:xsi', self::XML_NS_XSI);
        //$xmlRoot->setAttribute('xsi:schemaLocation', self::XML_NS . " " . self::XML_SCHEMA_SITEMAP);
        /* append it to the document created */
        $xmlRoot = $doc->appendChild($xmlRoot);

        /* create element for each location and append to root element */
        /** @var \Seo\Sitemap\SitemapUrl $url */
        foreach ($urls as $url) {
            $url = SitemapUrl::create($url);
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
     * @param \Seo\Sitemap\SitemapUrl[]|\Generator $urls Sitemap urls
     * @param array $options Options
     * @return \DOMDocument
     */
    public static function buildSitemapIndexXml($urls, array $options = []): \DOMDocument
    {
        $doc = static::initXml($options);

        /* create the root element of the xml tree */
        $xmlRoot = $doc->createElement("sitemapindex");
        /* set the namespace attribute */
        $xmlRoot->setAttribute('xmlns', self::XML_NS);
        /* set the required attributes for validation */
        //$xmlRoot->setAttribute('xmlns:xsi', self::XML_NS_XSI);
        //$xmlRoot->setAttribute('xsi:schemaLocation', sprintf("%s %s", self::XML_NS, self::XML_SCHEMA_INDEX));
        /* append it to the document created */
        $xmlRoot = $doc->appendChild($xmlRoot);

        /* create element for each location and append to root element */
        foreach ($urls as $url) {
            $url = SitemapUrl::create($url);
            if (!$url->isValid()) {
                continue;
            }

            $element = $xmlRoot->appendChild($doc->createElement("sitemap"));
            $element->appendChild($doc->createElement('loc', $url->loc));
            if ($url->lastmod) {
                $element->appendChild($doc->createElement('lastmod', $url->lastmod));
            }
        }

        return $doc;
    }

    /**
     * Validate sitemap xml
     *
     * @param \DOMDocument $xml The sitemap xml document
     * @return bool
     */
    public static function validateSitemapXml(\DOMDocument $xml): bool
    {
        $schema = Plugin::path('Seo') . DS . 'resources' . DS . 'schema' . DS . 'sitemap.xsd';

        return self::validateXml($xml, $schema);
    }

    /**
     * Validate sitemap xml
     *
     * @param \DOMDocument $xml The sitemap xml document
     * @return bool
     */
    public static function validateSitemapIndexXml(\DOMDocument $xml): bool
    {
        $schema = Plugin::path('Seo') . DS . 'resources' . DS . 'schema' . DS . 'siteindex.xsd';

        return self::validateXml($xml, $schema);
    }

    /**
     * Validate xml document against schema
     *
     * @param \DOMDocument $xml XML document
     * @param string $schemaFile Path to schema file
     * @return bool
     */
    protected static function validateXml(\DOMDocument $xml, string $schemaFile): bool
    {
        // bug: validation does not work with manually created xml documents
        // workaround: save the xml as string and create new document from xml string and then validate
        $xmlString = $xml->saveXML();
        $tmp = new \DOMDocument();
        $tmp->loadXML($xmlString);

        return $tmp->schemaValidate($schemaFile);
    }
}
