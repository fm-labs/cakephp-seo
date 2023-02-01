# Seo plugin for CakePHP

## Installation

You can install this plugin into your CakePHP application using [composer](http://getcomposer.org).

The recommended way to install composer packages is:

```
composer require fm-labs/cakephp-seo
```

## Features

- Robots.txt generator
- XML Sitemap generator
- XML Sitemap Index generator
- XML Sitemap schema validation
- Sitemaps support for XSL stylesheets
- TXT Sitemap Genrator
- Google Analytics Tracking

## Usage


## Robots TXT

The URL `/robots.txt` is automatically routed by the `Seo` plugin to the `RobotsController`.

By default, the `sitemapUrl` parameter is mapped to the `SitemapController`.
Override this, if you want to implement your own SitemapController and/or want to use your own route.

Supports following directives:

| Directive            | Config key | Description                                                                                                                                                                                        |
|----------------------|------------|----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------|
| Sitemap              | sitemapUrl | Some crawlers support a Sitemap directive, allowing multiple Sitemaps in the same robots.txt in the form Sitemap: full-url                                                                         |
| Host                 | hostName   | Some crawlers (Yandex) support a Host directive, allowing websites with multiple mirrors to specify their preferred domain                                                                         |
| Crawl-delay          | crawlDelay | The crawl-delay value is supported by some crawlers to throttle their visits to the host. Since this value is not part of the standard, its interpretation is dependent on the crawler reading it. |
| Allow/Disallow rules | rules      | List of rules. See examples.                                                                                                                                                                       |


### Configuration
```php
Configure::write('Seo.RobotsTxt', [
    'sitemapUrl' => '/path/to/custom/sitemap.xml',
    'crawlDelay' => 10,                 // Crawl-delay in seconds
    'rules' => [
        '*' => [                        // Default / All user agents
            '/' => true,                // Allow
        ],
        'google-bot' => [               // User agent
            '/' => true,                // Allow
            '/private' => false,        // Disallow
        ],
        
    ]
])
```

### Events

The `RobotsTxtBuilder` dispatches the `Seo.RobotsTxt.build` event, which can be used to modify the builder state via the event system.

```php
\Cake\Event\EventManager::instance()->on('Seo.RobotsTxt.build', function($event) {
    $robotsTxt = $event->getSubject();
    
    $rules = []; // put your rules here
    $robotsTxt->addRules($rules);
})
```

### Example output

```text
Sitemap: /path/to/custom/sitemap.xml

User-agent: *
Allow: /

User-agent: google-bot
Allow: /
Disallow: /private
```


## Sitemap XML

### Styling

Built-in Stylesheets:

* style-pedro: [pedroborges/xml-sitemap-stylesheet](https://github.com/pedroborges/xml-sitemap-stylesheet)
* style-catcto : [catcto/sitemap-stylesheet](https://github.com/catcto/sitemap-stylesheet)
