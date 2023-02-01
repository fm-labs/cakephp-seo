<?php
$this->loadHelper('Bootstrap.Tabs');

$this->Toolbar->addLink('Open robots.txt', '/robots.txt', ['target' => '_blank', 'data-icon' => 'external-link']);
$this->Toolbar->addLink('Open sitemap.xml', '/sitemap.xml', ['target' => '_blank', 'data-icon' => 'external-link']);

$this->assign('title', 'Seo')
?>
<div class="container-fluid">

    <?= $this->Tabs->create(); ?>
    <?= $this->Tabs->add('Robots TXT'); ?>
    <?= $this->Html->link('robots.txt', '/robots.txt', ['_target' => 'blank', 'class' => 'btn btn-primary']); ?>
    <?= $this->Html->tag('iframe', 'Iframe not supported', [
        'style' => 'height: 40vh;',
        'class' => 'w-100 border',
        'src' => '/robots.txt'
    ]); ?>


    <?= $this->Tabs->add('Sitemap XML'); ?>
    <?= $this->Html->link('sitemap.xml', '/sitemap.xml', ['_target' => 'blank', 'class' => 'btn btn-primary']); ?>
    <?= $this->Html->tag('iframe', 'Iframe not supported', [
        'style' => 'height: 40vh;',
        'class' => 'w-100 border',
        'src' => '/sitemap.xml'
    ]); ?>
    <?= $this->Tabs->render(); ?>
</div>
