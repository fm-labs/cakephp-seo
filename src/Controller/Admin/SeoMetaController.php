<?php

namespace Seo\Controller\Admin;

use Cake\Core\Configure;
use Cake\ORM\TableRegistry;
use Cake\Event\Event;

/**
 * Class SeoMetaController
 *
 * @package Seo\Controller\Admin
 */
class SeoMetaController extends AppController
{
    /**
     * @var array
     */
    protected $_metaTables = [];

    /**
     * @param Event $event
     * @return \Cake\Http\Response|null|void
     */
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

    /**
     * Index action
     */
    public function index()
    {
        $tables = $this->_metaTables;
        $table = $this->request->getQuery('table');

        $contents = $metas = null;
        if (isset($this->_metaTables[$table])) {
            $tableOpt = $this->_metaTables[$table];

            $Table = TableRegistry::getTableLocator()->get($table);
            $contents = $Table->find('list', $tableOpt)->all()->toArray();

            $Metas = TableRegistry::getTableLocator()->get('Content.PageMetas');
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
