<?php

namespace Bolt\Lib;

use Bolt\Utils\{URL};

class Sitemap
{
    static function init()
    {
        error_reporting(0);
        header('Content-Type: application/xml; charset=utf-8');
    }

    static function generate(array $pages)
    {

        $dom = new \DOMDocument('1.0', 'UTF-8');
        $urlset = $dom->createElement('urlset');
        $urlset->setAttribute('xmlns', 'http://www.sitemaps.org/schemas/sitemap/0.9');
        $dom->appendChild($urlset);

        $protocol = URL::getServerProtocol();

        foreach ($pages as $page) {
            $url = $dom->createElement('url');

            $loc = $dom->createElement('loc', htmlspecialchars("$protocol://{$_SERVER["HTTP_HOST"]}{$page['loc']}"));
            $url->appendChild($loc);

            $lastmod = $dom->createElement('lastmod', htmlspecialchars($page['lastmod'] ?? date('Y-m-d H:i:s')));
            $url->appendChild($lastmod);

            $changefreq = $dom->createElement('changefreq', htmlspecialchars($page['changefreq'] ?? "daily"));
            $url->appendChild($changefreq);

            $priority = $dom->createElement('priority', htmlspecialchars($page['priority'] ?? '0.8'));
            $url->appendChild($priority);

            $urlset->appendChild($url);
        }

        echo $dom->saveXML();
    }
}
