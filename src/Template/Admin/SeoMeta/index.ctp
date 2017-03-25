<?php $this->loadHelper('Text'); ?>
<div class="index">

    <div class="well">
        <?php foreach($tables as $_table => $_tableOpts): ?>
            <?= $this->Html->link($_table, ['action' => 'index', 'table' => $_table], ['class' => 'btn btn-default']); ?>&nbsp;
        <?php endforeach; ?>
    </div>

    <?php if ($table): ?>
        <h2><?= __('Seo Meta for {0}', $table); ?></h2>

        <table class="table table-compact table-striped table-hover">
            <tr>
                <th>Id</th>
                <th>Title</th>
                <th>Lang</th>
                <th>Description</th>
                <th>Keywords</th>
                <th class="actions">Actions</th>
            </tr>
            <?php foreach ($contents as $id => $meta): ?>
                <?php if (is_object($meta)): ?>
                    <tr>
                        <td><?= h($id); ?>*</td>
                        <td><span style="font-style: italic;"><?= h($meta->_title); ?></span><br /><?= h($meta->title); ?>&nbsp;(<?= h(strlen($meta->title)); ?>)</td>
                        <td><?= h($meta->lang); ?></td>
                        <td><?= h($this->Text->truncate($meta->description, 200)); ?>&nbsp;(<?= h(strlen($meta->description)); ?>)</td>
                        <td><?= h($this->Text->truncate($meta->keywords, 300)); ?>&nbsp;(<?= h(count(explode(',', $meta->keywords))); ?>)</td>
                        <td class="actions">
                            <?= $this->Html->link('Edit',
                                ['plugin' => 'Banana', 'controller' => 'PageMetas', 'action' => 'edit', $meta->id],
                                ['class' => 'btn btn-default link-frame-modal']); ?>
                        </td>
                    </tr>
                <?php else: ?>
                    <tr>
                        <td><?= h($id); ?></td>
                        <td><?= h($meta); ?></td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                    </tr>
                <?php endif; ?>

            <?php endforeach; ?>
        </table>

    <?php endif; ?>

    <?php debug($table); ?>
    <?php debug($tables); ?>
    <?php debug($contents); ?>
    <?php debug($metas); ?>
</div>