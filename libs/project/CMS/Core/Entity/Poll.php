<?php
namespace CMS\Core\Entity;

use Delorius\Core\ORM;

/**
 * @property int $poll_id Primary key
 * @property string $text Question (max=400)
 * @property int $status Status poll
 * @property int $count Count answers
 * @property int $date_cr Date time created
 * @property int $date_edit Date time edit
 */
class Poll extends ORM
{

    /**
     * @return $this
     */
    public function active()
    {
        $this->where('status', '=', 1);
        return $this;
    }

    /**
     * @return $this
     */
    public function sort()
    {
        $this->order_updated('desc')->order_pk();
        return $this;
    }

    public function rules()
    {
        return array(
            'name' => array(
                array('\\Delorius\\Utils\\Validators::isText', array(':value', 1, 200), 'Укажите навания опроса'),
            ),
            'text' => array(
                array('\\Delorius\\Utils\\Validators::isText', array(':value', 1, 600), 'Укажите коментарий опроса'),
            ),
        );
    }

    protected function behaviors()
    {
        return array(
            'editPollBehavior' => 'CMS\Core\Behaviors\EditPollBehavior',
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

    protected $_primary_key = 'poll_id';
    protected $_table_name = 'df_poll';
    protected $_table_columns_set = array('text');

    protected $_table_columns = array(
        'poll_id' => array(
            'column_name' => 'poll_id',
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
        'text' => array(
            'column_name' => 'text',
            'data_type' => 'varchar',
            'character_maximum_length' => 600,
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
            'column_default' => 0,
        ),
        'status' => array(
            'column_name' => 'status',
            'data_type' => 'tinyint unsigned',
            'display' => 1,
            'column_default' => 0,
        ),
    );
}