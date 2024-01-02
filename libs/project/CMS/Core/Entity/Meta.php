<?php

namespace CMS\Core\Entity;

use CMS\Core\Helper\Helpers;
use CMS\Core\Helper\ParserString;
use Delorius\Core\ORM;
use Delorius\Utils\Strings;

class Meta extends ORM
{
    /**
     * @var ORM
     */
    protected $_orm_owner;

    /**
     * @var string title
     */
    protected $_title;

    /**
     * @var bool
     */
    protected $_change_title = false;

    /**
     * @var string
     */
    protected $_desc;

    /**
     * @var bool
     */
    protected $_change_desc = false;

    /**
     * @var null ParserString
     */
    protected $_parserString = null;

    /**
     * @param ParserString $parserString
     * @return $this
     */
    public function setParser(ParserString $parserString)
    {
        $this->_parserString = $parserString;
        return $this;
    }

    /**
     * @return ParserString
     */
    public function getParser()
    {
        if (!$this->_parserString) {
            return new ParserString();
        }

        return $this->_parserString;
    }

    /**
     * @param $value
     * @return string
     */
    public function parserData($value)
    {
        return $this->getParser()->render($value);
    }

    /**
     * @param string $value
     */
    public function setTitle($value)
    {
        $this->_change_title = true;
        $this->_title = $value;
    }

    /**
     * @return bool
     */
    public function isChangeTitle()
    {
        return $this->_change_desc;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->_title;
    }

    /**
     * @param string $value
     */
    public function setDesc($value)
    {
        $this->_change_desc = true;
        $this->_desc = $value;
    }

    /**
     * @return string
     */
    public function getDesc()
    {
        return $this->_desc;
    }

    /**
     * @return bool
     */
    public function isChangeDesc()
    {
        return $this->_change_desc;
    }

    /**
     * @param ORM $orm
     * @return $this
     */
    public function setOwner(ORM $orm)
    {
        $this->_orm_owner = $orm;
        $this->target_owner = Helpers::getTableId($orm);
        $this->target_id = $orm->pk();
        return $this;
    }

    /** @return \CMS\Core\Entity\Meta */
    public static function loadByOwner(ORM $orm)
    {
        $meta = self::model()
            ->where('target_owner', '=', Helpers::getTableId($orm))
            ->where('target_id', '=', $orm->pk())
            ->find();
        if (!$meta->loaded()) {
            $meta->setOwner($orm);
        }
        return $meta;
    }

    public function values(array $values, array $expected = NULL)
    {
        if (isset($values['options']) && count($values['options'])) {
            $options = $values['options'];
            unset($values['options']);
            $this->onAfterSave[] = function (ORM $orm) use ($options) {

                $result = array();
                foreach ($options as $code => $opts) {
                    if (count($opts)) {
                        foreach ($opts as $name => $value) {
                            $result[] = array(
                                'code' => $code,
                                'name' => $name,
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

    public function as_array()
    {
        $arr = parent::as_array();

        $options = $this->getOptions();
        foreach ($options as $opt) {
            $arr['options'][$opt['code']][$opt['name']] = $opt['value'];
        }

        return $arr;
    }

    protected function filters()
    {
        return array(
            TRUE => array(
                array('trim'),
                array(array($this, 'truncate'))
            ),
        );
    }

    protected function rules()
    {
        return array(
            'target_id' => array(
                array(array($this, 'hasOwnerId'), array(':value'), 'Используйте метод Meta::setOwner'),
            ),
            'target_owner' => array(
                array(array($this, 'hasOwnerName'), array(':value'), 'Используйте метод Meta::setOwner'),
            ),
        );
    }

    protected function hasOwnerName($value)
    {
        if (
            $this->_orm_owner == null ||
            $value != Helpers::getTableId($this->_orm_owner)
        ) {
            return false;
        }

        return true;
    }

    protected function hasOwnerId($value)
    {
        if ($this->_orm_owner == null || $value != $this->_orm_owner->pk()) {
            return false;
        }
        return true;
    }

    protected function truncate($value = null)
    {
        if (is_string($value)) {
            $value = Strings::truncate($value, 197);
        }
        return $value;
    }

    protected function behaviors()
    {
        return array(
            'optionsBehavior' => 'CMS\Core\Behaviors\OptionsBehavior'
        );
    }

    protected $_table_columns_set = array('target_id', 'target_owner');
    protected $_primary_key = 'meta_id';
    protected $_table_name = 'df_meta';

    protected $_table_columns = array(
        'meta_id' => array(
            'data_type' => 'int unsigned',
            'display' => 11,
            'column_name' => 'meta_id',
            'extra' => 'auto_increment',
            'key' => 'PRI',
        ),
        'title' => array(
            'column_name' => 'title',
            'data_type' => 'varchar',
            'character_maximum_length' => 200,
            'collation_name' => 'utf8_general_ci',
        ),
        'keys' => array(
            'column_name' => 'keys',
            'data_type' => 'varchar',
            'character_maximum_length' => 200,
            'collation_name' => 'utf8_general_ci',
        ),
        'desc' => array(
            'column_name' => 'desc',
            'data_type' => 'varchar',
            'character_maximum_length' => 200,
            'collation_name' => 'utf8_general_ci',
        ),
        'redirect' => array(
            'column_name' => 'redirect',
            'data_type' => 'varchar',
            'character_maximum_length' => 200,
            'collation_name' => 'utf8_general_ci',
        ),
        'target_owner' => array(
            'column_name' => 'target_owner',
            'data_type' => 'int unsigned',
            'display' => 11,
        ),
        'target_id' => array(
            'column_name' => 'target_id',
            'data_type' => 'int unsigned',
            'display' => 11,
        ),
    );
}