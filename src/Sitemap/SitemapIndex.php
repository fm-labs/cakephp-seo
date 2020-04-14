<?php
declare(strict_types=1);

namespace Seo\Sitemap;

use Cake\Core\Plugin;

/**
 * Class Sitemap
 *
 * @package Seo\Sitemap
 */
class SitemapIndex extends Sitemap
{
    /**
     * {@inheritDoc}
     */
    public function __construct(array $urls = [])
    {
        parent::__construct($urls);

        $this->xmlValidationFile = Plugin::path('Seo') . DS . 'resources' . DS . 'schema' . DS . 'siteindex.xsd';
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
        $xmlRoot = $doc->createElement("sitemapindex");
        /* set the namespace attribute */
        $xmlRoot->setAttribute('xmlns', self::XML_NS);
        /* set the required attributes for validation */
        if ($options['validationNs'] === true) {
            $xmlRoot->setAttribute('xmlns:xsi', self::XML_NS_XSI);
            $xmlRoot->setAttribute('xsi:schemaLocation', sprintf("%s %s", self::XML_NS, self::XML_SCHEMA_INDEX));
        }
        /* append it to the document created */
        $xmlRoot = $doc->appendChild($xmlRoot);

        /* create element for each location and append to root element */
        foreach ($this->getUrls() as $url) {
            $url = SitemapUrl::createFrom($url);
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
}
