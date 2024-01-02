<?php
namespace Location\Core\Entity;

use Delorius\Core\Environment;
use Delorius\Core\ORM;
use Delorius\Utils\Strings;

class City extends ORM
{

    /**
     * @return $this
     */
    public function main()
    {
        $this->where($this->table_name() . '.main', '=', 1);
        return $this;
    }

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
     * @param string $direction
     * @return $this
     */
    public function sortByCountry($direction = 'DESC')
    {
        $country = new Country();
        $this->order_by($country->table_name() . '.pos', $direction)
            ->order_by($country->table_name() . '.id');
        return $this;
    }

    /**
     * @param $countryId
     * @return $this
     */
    public function whereCountry($countryId)
    {
        $this->where($this->table_name() . '.country_id', '=', $countryId);
        return $this;
    }

    /**
     * Возращает массив данных включаю страну
     * @return $this
     */
    public function selectCountry()
    {
        $country = new Country();
        $this->select(
            array($this->table_name() . '.id', 'id'),
            array($this->table_name() . '.main', 'main'),
            array($this->table_name() . '.status', 'status'),
            array($this->table_name() . '.name', 'name'),
            array($this->table_name() . '.name_2', 'name_2'),
            array($this->table_name() . '.name_3', 'name_3'),
            array($this->table_name() . '.name_4', 'name_4'),
            array($this->table_name() . '.url', 'url'),
            array($this->table_name() . '.pos', 'pos'),
            array($country->table_name() . '.id', 'country_id'),
            array($country->table_name() . '.name', 'country_name'),
            array($country->table_name() . '.url', 'country_url')
        )
            ->join($country->table_name(), 'LEFT')
            ->on($this->table_name() . '.country_id', '=', $country->table_name() . '.id');
        return $this;
    }

    public function values(array $values, array $expected = NULL)
    {
        if (isset($values['options']) && count($values['options'])) {
            $options = $values['options'];
            unset($values['options']);
            $fields = Environment::getContext()->getParameters('location.city');
            $this->onAfterSave[] = function (ORM $orm) use ($options, $fields) {
                $result = array();
                foreach ($options as $code => $opts) {
                    if (count($opts)) {
                        foreach ($opts as $name => $value) {
                            $result[] = array(
                                'code' => $code,
                                'name' => $fields[$code],
                                'value' => $value
                            );
                        }
                    }
                }

                $orm->mergeOptions($result);
            };
        }

        parent::values($values, $expected);
    }

    protected $_table_columns_set = array();
    protected $_primary_key = 'id';
    protected $_table_name = 'loc_cities';

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

    protected function behaviors()
    {
        return array(
            'optionsBehavior' => 'CMS\Core\Behaviors\OptionsBehavior',
            'metaDataBehavior' => array(
                'class' => 'CMS\Core\Behaviors\MetaDataBehavior',
                'desc' => '{text}',
            ),
            'imageBehavior' => array(
                'class' => 'CMS\Core\Behaviors\ImageBehavior',
                'path' => 'city',
                'ratio_fill' => true,
                'preview_width' => 200,
                'preview_height' => 200,
                'normal_width' => 1920,
                'normal_height' => 550,
            )
        );
    }


    protected $_created_column = array(
        'column' => 'date_cr',
        'format' => TRUE,
    );

    protected $_updated_column = array(
        'column' => 'date_edit',
        'format' => TRUE,
    );

    protected $_table_columns = array(
        'id' => array(
            'column_name' => 'id',
            'data_type' => 'int unsigned',
            'display' => 11,
            'extra' => 'auto_increment',
            'key' => 'PRI'
        ),
        'external_id' => array(
            'column_name' => 'external_id',
            'data_type' => 'varchar',
            'character_maximum_length' => 36,
            'collation_name' => 'utf8_general_ci',
        ),
        'external_change' => array(
            'column_name' => 'external_change',
            'data_type' => 'tinyint unsigned',
            'display' => 1,
            'column_default' => 1
        ),
        'country_id' => array(
            'column_name' => 'country_id',
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
        'main' => array(
            'column_name' => 'main',
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
        'name_2' => array(
            'column_name' => 'name_2',
            'data_type' => 'varchar',
            'character_maximum_length' => 200,
            'collation_name' => 'utf8_general_ci',
        ),
        'name_3' => array(
            'column_name' => 'name_3',
            'data_type' => 'varchar',
            'character_maximum_length' => 200,
            'collation_name' => 'utf8_general_ci',
        ),
        'name_4' => array(
            'column_name' => 'name_4',
            'data_type' => 'varchar',
            'character_maximum_length' => 200,
            'collation_name' => 'utf8_general_ci',
        ),
        'text' => array(
            'column_name' => 'text',
            'data_type' => 'varchar',
            'character_maximum_length' => 1000,
            'collation_name' => 'utf8_general_ci',
        ),
        'pos' => array(
            'column_name' => 'pos',
            'data_type' => 'int unsigned',
            'display' => 11,
            'column_default' => 0
        ),
        'prefix' => array(
            'column_name' => 'prefix',
            'data_type' => 'varchar',
            'character_maximum_length' => 50,
            'collation_name' => 'utf8_general_ci',
        ),
        'date_cr' => array(
            'column_name' => 'date_cr',
            'data_type' => 'int unsigned',
            'display' => 11,
        ),
        'date_edit' => array(
            'column_name' => 'date_edit',
            'data_type' => 'int unsigned',
            'display' => 11,
            'column_default' => 0
        )
    );

}