<?php

namespace Seo\Controller\Admin;


use Cake\Core\Configure;
use Cake\Network\Exception\BadRequestException;
use Cake\Network\Exception\NotFoundException;
use Cake\ORM\TableRegistry;
use Cake\Event\Event;

class SeoMetaController extends AppController
{
    protected $_metaTables = [];

    public function beforeFilter(Event $event)
    {
        parent::beforeFilter($event);

        $tables = Configure::read('Seo.Meta.tables');
        foreach ($tables as $tableName => $options) {
            if (is_numeric($tableName)) {
                $tableName = $options;
                $options = [];
            }

            $this->_metaTables[$tableName] = $options;
        }
    }

    public function index()
    {

        $tables = $this->_metaTables;
        $table = $this->request->query('table');

        $contents = $metas = null;
        if (isset($this->_metaTables[$table])) {
            $tableOpt = $this->_metaTables[$table];

            $Table = TableRegistry::get($table);
            $contents = $Table->find('list', $tableOpt)->all()->toArray();

            $Metas = TableRegistry::get('Content.PageMetas');
            $metas = $Metas->find()->where(['model' => $table])->all();

            foreach ($metas as $meta) {
                if (isset($contents[$meta['foreignKey']])) {
                    $meta->_title = $contents[$meta['foreignKey']];
                    $contents[$meta['foreignKey']] = $meta;
                }
            }
        }


        $this->set(compact('table', 'tables', 'contents', 'meta'));
    }
}