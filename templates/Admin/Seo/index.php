<?php
$this->loadHelper('Bootstrap.Tabs');

$robotsTxtUrl = '/robots.txt';
$sitemapIndexUrl = '/sitemap.xml';

$this->Toolbar->addLink('Open robots.txt', $robotsTxtUrl, ['target' => '_blank', 'data-icon' => 'external-link']);
$this->Toolbar->addLink('Open sitemap.xml', $sitemapIndexUrl, ['target' => '_blank', 'data-icon' => 'external-link']);

$this->assign('title', 'Seo')
?>
<div class="container-fluid">

    <?= $this->Tabs->create(); ?>
    <?= $this->Tabs->add('Robots TXT'); ?>
    <?= $this->Html->tag('iframe', 'Iframe not supported', [
        'style' => 'height: 40vh;',
        'class' => 'w-100 border',
        'src' => $robotsTxtUrl
    ]); ?>


    <?= $this->Tabs->add('Sitemap XML'); ?>
    <?= $this->Html->tag('iframe', 'Iframe not supported', [
        'style' => 'height: 40vh;',
        'class' => 'w-100 border',
        'src' =>$sitemapIndexUrl
    ]); ?>
    <?= $this->Tabs->render(); ?>
</div>
