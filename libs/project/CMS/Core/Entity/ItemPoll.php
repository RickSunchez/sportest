<?php
namespace CMS\Core\Entity;

use Delorius\Core\ORM;
use Delorius\Utils\Strings;

/**
 * @property int $item_id Primary key
 * @property string $name Option (max=200)
 * @property int $poll_id Id by poll
 * @property int $pos Priority
 * @property int $count Count answers
 */
class ItemPoll extends ORM
{

    /**
     * @return string
     */
    public function str_vote()
    {
        return $this->count . ' ' . Strings::pluralForm($this->count, 'голос', 'голоса', 'голосов');
    }

    /**
     * @return $this
     */
    public function sort()
    {
        $this->order_by('pos', 'desc')->order_pk();
        return $this;
    }

    public function rules()
    {
        return array(
            'name' => array(
                array('\\Delorius\\Utils\\Validators::isText', array(':value', 1, 200), 'Укажите вариант ответа'),
            ),
        );
    }

    public function as_array()
    {
        $arr = parent::as_array();
        $arr['vote'] = $this->str_vote();
        return $arr;
    }


    protected $_primary_key = 'item_id';
    protected $_table_name = 'df_poll_item';

    protected $_table_columns = array(
        'item_id' => array(
            'column_name' => 'item_id',
            'data_type' => 'int unsigned',
            'display' => 11,
            'extra' => 'auto_increment',
            'key' => 'PRI'
        ),
        'name' => array(
            'column_name' => 'name',
            'data_type' => 'varchar',
            'character_maximum_length' => 200,
            'collation_name' => 'utf8_general_ci',
        ),
        'poll_id' => array(
            'column_name' => 'poll_id',
            'data_type' => 'int unsigned',
            'display' => 11,
        ),
        'pos' => array(
            'column_name' => 'pos',
            'data_type' => 'int unsigned',
            'display' => 11,
            'column_default' => 0,
        ),
        'count' => array(
            'column_name' => 'count',
            'data_type' => 'int unsigned',
            'display' => 11,
            'column_default' => 0,
        ),
    );
}