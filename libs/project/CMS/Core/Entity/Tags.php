<?php
namespace CMS\Core\Entity;

use CMS\Core\Helper\Helpers;
use Delorius\Core\ORM;
use Delorius\Utils\Strings;
use Delorius\View\Html;

class Tags extends ORM
{

    /**
     * @return $this
     */
    public function active()
    {
        $this->where($this->table_name() . '.status', '=', 1);
        return $this;
    }

    /**
     * @return $this
     */
    public function sort($direction = 'DESC')
    {
        $this->order_by('pos', $direction)->order_by('target_type')->order_pk();
        return $this;
    }

    /**
     * @param string $name
     * @return $this
     */
    public function whereUrl($url)
    {
        $this->where($this->table_name() . '.url', '=', $this->filterName($url));
        return $this;
    }

    /**
     * @param string $name
     * @return $this
     */
    public function whereName($name)
    {
        $this->where($this->table_name() . '.name', '=', $this->filterName($name));
        return $this;
    }

    /**
     * @param ORM $orm
     * @return $this
     */
    public function whereByTargetType(ORM $orm)
    {
        $this->where($this->table_name() . '.target_type', '=', Helpers::getTableId($orm));
        return $this;
    }

    public function rules()
    {
        return array(
            'name' => array(
                array('\\Delorius\\Utils\\Validators::isText', array(':value'), 'Укажите название тега'),
            ),
        );
    }

    protected function filters()
    {
        return array(
            TRUE => array(
                array('trim')
            ),
            'url' => array(
                array(array($this, 'translate'))
            ),
            'name' => array(
                array(array($this, 'filterName'))
            ),
            'show' => array(
                array(array($this, 'showName'))
            ),
        );
    }


    protected function showName($value)
    {
        if (!$value) {
            $value = $this->filterName($this->name);
        }

        return $value;
    }

    protected function translate($value)
    {
        if ($value == null) {
            $value = $this->name;
        }
        $value = Strings::webalize(Strings::translit(Strings::trim($value)));
        return $value;
    }

    protected function filterName($value)
    {
        return Strings::lower(Html::clearTags($value));
    }

    protected function behaviors()
    {
        return array(
            'metaDataBehavior' => array(
                'class' => 'CMS\Core\Behaviors\MetaDataBehavior',
            ),
        );
    }

    protected $_primary_key = 'tag_id';
    protected $_table_name = 'df_tags';
    protected $_table_columns_set = array('name', 'show', 'url');

    protected $_table_columns = array(
        'tag_id' => array(
            'column_name' => 'tag_id',
            'data_type' => 'int unsigned',
            'display' => 11,
            'extra' => 'auto_increment',
            'key' => 'PRI'
        ),
        'name' => array(
            'column_name' => 'name',
            'data_type' => 'varchar',
            'character_maximum_length' => 100,
            'collation_name' => 'utf8_general_ci',
        ),
        'url' => array(
            'column_name' => 'url',
            'data_type' => 'varchar',
            'character_maximum_length' => 100,
            'collation_name' => 'utf8_general_ci',
        ),
        'status' => array(
            'column_name' => 'status',
            'data_type' => 'tinyint unsigned',
            'display' => 1,
            'column_default' => 0,
        ),
        'show' => array(
            'column_name' => 'show',
            'data_type' => 'varchar',
            'character_maximum_length' => 100,
            'collation_name' => 'utf8_general_ci',
        ),
        'text' => array(
            'column_name' => 'text',
            'data_type' => 'varchar',
            'character_maximum_length' => 500,
            'collation_name' => 'utf8_general_ci',
        ),
        'target_type' => array(
            'column_name' => 'target_type',
            'data_type' => 'int unsigned',
            'display' => 11,
        ),
        'pos' => array(
            'column_name' => 'pos',
            'data_type' => 'int unsigned',
            'display' => 11,
            'column_default' => 0,
        ),
    );
}