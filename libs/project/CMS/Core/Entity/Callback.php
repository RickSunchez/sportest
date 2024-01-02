<?php
namespace CMS\Core\Entity;

use Delorius\Core\DateTime;
use Delorius\Core\ORM;
use Delorius\Utils\Strings;
use Delorius\View\Html;

class Callback extends ORM
{

    /**
     * @return string
     * @throws \Delorius\Exception\Error
     */
    public function renderLink()
    {
        $a = Html::el('a');
        $a->href(link_to('callback_active'), array('hash' => $this->code, 'id' => $this->pk()));
        $a->addAttributes(array('target' => '_black'));
        $a->setHtml('Отметить как прочитанное');
        return $a->render();
    }

    public function as_array()
    {
        $arr = parent::as_array();
        $config = $this->getConfig();
        if (count($config['form']))
            foreach ($config['form'] as $name => $value) {
                $arr['form'][] = array(
                    'name' => _t('CMS:Core', $name),
                    'value' => $value
                );
            }
        $arr['created'] = DateTime::dateFormat($arr[$this->_created_column['column']], true);
        $arr['finished'] = $arr['date_finished'] ? DateTime::dateFormat($arr['date_finished'], true) : null;
        return $arr;
    }

    protected function rules()
    {
        return array(
            'subject' => array(
                array('\\Delorius\\Utils\\Validators::isText', array(':value', 0, 200), 'Заполните форму'),
            )
        );
    }

    protected function filters()
    {
        return array(
            TRUE => array(
                array('trim')
            ),

            'code' => array(
                array(array($this, 'codeGen'))
            ),
        );
    }

    protected function checkContact($value)
    {
        if ($value != null) {
            return true;
        }
        if ($this->email != null) {
            return true;
        }
        return false;
    }


    protected function codeGen($code = null)
    {
        $code = $code ? $code : Strings::random(32, '0-9a-zA-Z');
        return $code;
    }

    protected $_primary_key = 'callback_id';
    protected $_table_name = 'df_callback';
    protected $_config_key = 'config';
    protected $_table_columns_set = array('code', 'subject');


    protected $_created_column = array(
        'column' => 'date_cr',
        'format' => TRUE,
    );

    protected $_updated_column = array(
        'column' => 'date_edit',
        'format' => TRUE,
    );


    protected $_table_columns = array(
        'callback_id' => array(
            'column_name' => 'callback_id',
            'data_type' => 'int unsigned',
            'display' => 11,
            'extra' => 'auto_increment',
            'key' => 'PRI'
        ),
        'user_id' => array(
            'column_name' => 'user_id',
            'data_type' => 'int unsigned',
            'display' => 11,
            'column_default' => 0
        ),
        'subject' => array(
            'column_name' => 'subject',
            'data_type' => 'varchar',
            'character_maximum_length' => 200,
            'collation_name' => 'utf8_general_ci',
        ),
        'code' => array(
            'column_name' => 'code',
            'data_type' => 'varchar',
            'character_maximum_length' => 32,
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
        ),
        'date_finished' => array(
            'column_name' => 'date_finished',
            'data_type' => 'int unsigned',
            'display' => 11,
            'column_default' => 0
        ),
        'config' => array(
            'column_name' => 'config',
            'data_type' => 'text',
            'collation_name' => 'utf8_general_ci',
        ),


    );
}