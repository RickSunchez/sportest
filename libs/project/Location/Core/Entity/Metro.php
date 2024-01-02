<?php
namespace Location\Core\Entity;

use Delorius\Core\ORM;
use Delorius\Utils\Strings;

class Metro extends ORM
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
     * @param null $direction
     * @return $this
     */
    public function sort($direction = 'DESC')
    {
        $this->order_by($this->table_name() . '.pos', $direction)
            ->order_by($this->table_name() . '.name', 'ASC')
            ->order_pk();
        return $this;
    }

    /**
     * @param $countryId
     * @return $this
     */
    public function whereCity($countryId)
    {
        $this->where($this->table_name() . '.city_id', '=', $countryId);
        return $this;
    }

    protected $_table_columns_set = array();
    protected $_primary_key = 'id';
    protected $_table_name = 'loc_metro';

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
                array(array($this, 'setName'))
            ),
        );
    }

    protected function rules()
    {
        return array(
            'name' => array(
                array('\\Delorius\\Utils\\Validators::isText', array(':value', 1, 196), 'Укажите название'),
            ),
        );
    }

    protected function translate($value)
    {
        if ($value == null) {
            $value = $this->name;
        }
        $value = Strings::webalize(Strings::translit(Strings::trim($value)));
        return $value;
    }

    protected function setName($value)
    {
        if ($this->url == null) {
            $this->url = $value;
        }
        $this->url = Strings::webalize(Strings::translit(Strings::trim($this->url)));
        return $value;
    }

    protected $_table_columns = array(
        'id' => array(
            'column_name' => 'id',
            'data_type' => 'int unsigned',
            'display' => 11,
            'extra' => 'auto_increment',
            'key' => 'PRI'
        ),
        'city_id' => array(
            'column_name' => 'city_id',
            'data_type' => 'int unsigned',
            'display' => 11,
            'column_default' => 0
        ),
        'status' => array(
            'column_name' => 'status',
            'data_type' => 'tinyint unsigned',
            'display' => 1,
            'column_default' => 0,
        ),
        'url' => array(
            'column_name' => 'url',
            'data_type' => 'varchar',
            'character_maximum_length' => 200,
            'collation_name' => 'utf8_general_ci',
        ),
        'name' => array(
            'column_name' => 'name',
            'data_type' => 'varchar',
            'character_maximum_length' => 200,
            'collation_name' => 'utf8_general_ci',
        ),
        'pos' => array(
            'column_name' => 'pos',
            'data_type' => 'int unsigned',
            'display' => 11,
            'column_default' => 0
        ),
        'locations' => array( // xxx,yyyy
            'column_name' => 'locations',
            'data_type' => 'varchar',
            'character_maximum_length' => 50,
            'collation_name' => 'utf8_general_ci',
        ),
        'prefix' => array(
            'column_name' => 'prefix',
            'data_type' => 'varchar',
            'character_maximum_length' => 50,
            'collation_name' => 'utf8_general_ci',
        )
    );

}