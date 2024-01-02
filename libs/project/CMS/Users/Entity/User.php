<?php
namespace CMS\Users\Entity;

use Delorius\Core\DateTime;
use Delorius\Core\Environment;
use Delorius\Core\ORM;
use Delorius\Utils\Strings;

/**
 * Class User
 * @package CMS\Users\Entity
 *
 * @property int $user_id
 * @property string $email (max=200)
 * @property string $password (max=40)
 * @property string $login (max=100)
 * @property string $ip (max=200)
 * @property string $role (max=200)
 * @property int $active (1=yes,0=no)
 * @property string $hash (max=40)
 * @property int $date_cr
 * @property int $date_edit
 * @property int $date_last_login
 */
class User extends ORM
{

    /**
     * @param string $field
     * @param string $direction
     * @return $this
     */
    public function sort($field = 'email', $direction = 'desc')
    {
        $this->order_by($field, $direction)->order_pk();
        return $this;
    }

    /**
     * @param array $ids
     * @return $this
     */
    public function whereUserIds($ids)
    {
        $this->where('user_id', 'in', $ids);
        return $this;
    }


    /**
     * @return $this
     */
    public function active()
    {
        $this->where('active', '=', 1);
        return $this;
    }

    protected $_primary_key = 'user_id';
    protected $_table_name = 'df_users';
    protected $_table_columns_set = array('email', 'role');

    protected $_created_column = array(
        'column' => 'date_cr',
        'format' => TRUE,
    );

    protected $_updated_column = array(
        'column' => 'date_edit',
        'format' => TRUE, // 'd.m.Y H:i'
    );

    protected function filters()
    {
        return array(
            TRUE => array(
                array('trim')
            ),
            'password' => array(
                array(array($this, 'hashPassword'))
            ),
            'email' => array(
                array(array($this, 'setLogin'))
            ),
            'role' => array(
                array(array($this, 'setRole'))
            ),
        );
    }

    public function rules()
    {
        return array(
            'email' => array(
                array('\\Delorius\\Utils\\Validators::isEmail', array(':value'), _t('CMS:Users', 'Email Add a valid')),
                array(array($this, 'unique'), array('email', ':value'), _t('CMS:Users', 'E-mail used by another user'))
            ),
            'password' => array(
                array('\\Delorius\\Utils\\Validators::isText', array(':value'), _t('CMS:Users', 'Enter your password')),
            )
        );
    }

    public function setRole($value)
    {
        if ($value == null) {
            return Environment::getContext()->getService('user')->authenticatedRole;
        }
        $arr = explode(',', $value);
        $arr = array_map('trim', $arr);
        return implode(',', $arr);
    }

    public function hashPassword($value)
    {
        if (!$this->loaded()) {
            $this->salt = Strings::random(6);
        }

        $salt = $this->salt ?
            $this->salt :
            '(&4pnr74cray)Mt[mom-)_&#d05nd;cDt6,kw[M)M*{#flkxjmxlkj';
        return Strings::codePassword($value . $salt);
    }

    public function setLogin($value)
    {
        if ($this->login) {
            return $value;
        }
        $list = explode('@', $value);
        $this->login = Strings::webalize($list[0]);
        return $value;
    }

    public function as_array()
    {
        $arr = parent::as_array();
        $arr['created'] = DateTime::dateFormat($arr[$this->_created_column['column']], true);
        $arr['last_logged_in'] = $arr['date_last_login'] ? DateTime::dateFormat($arr['date_last_login'], true) : '';
        unset($arr['password'], $arr['hash'],$arr['salt']);
        return $arr;
    }

    protected function behaviors()
    {
        return array(
            'editUsersBehavior' => 'CMS\Users\Behaviors\EditUserBehavior',
            'attrUserBehavior' => 'CMS\Users\Behaviors\AttrUserBehavior',
            'messageBehavior' => 'CMS\Users\Behaviors\MessageBehavior',
            'imageBehavior' => array(
                'class' => 'CMS\Core\Behaviors\ImageBehavior',
                'path' => 'avatar',
                'crop' => true,
                'preview_width' => 280,
                'preview_height' => 330,
            ),
        );
    }

    protected $_table_columns = array(
        'user_id' => array(
            'column_name' => 'user_id',
            'data_type' => 'int unsigned',
            'display' => 11,
            'extra' => 'auto_increment',
            'key' => 'PRI'
        ),
        'email' => array(
            'column_name' => 'email',
            'data_type' => 'varchar',
            'character_maximum_length' => 200,
            'collation_name' => 'utf8_general_ci',
        ),
        'password' => array(
            'column_name' => 'password',
            'data_type' => 'varchar',
            'character_maximum_length' => 40,
            'collation_name' => 'utf8_general_ci',
        ),
        'login' => array(
            'column_name' => 'login',
            'data_type' => 'varchar',
            'character_maximum_length' => 100,
            'collation_name' => 'utf8_general_ci',
        ),
        'ip' => array(
            'column_name' => 'ip',
            'data_type' => 'varchar',
            'character_maximum_length' => 200,
            'collation_name' => 'utf8_general_ci',
        ),
        'role' => array(
            'column_name' => 'role',
            'data_type' => 'varchar',
            'character_maximum_length' => 200,
            'collation_name' => 'utf8_general_ci',
        ),
        'active' => array(
            'column_name' => 'active',
            'data_type' => 'tinyint unsigned',
            'display' => 1,
            'column_default' => 1
        ),
        'salt' => array(
            'column_name' => 'salt',
            'data_type' => 'varchar',
            'character_maximum_length' => 6,
            'collation_name' => 'utf8_general_ci',
        ),
        'hash' => array(
            'column_name' => 'hash',
            'data_type' => 'varchar',
            'character_maximum_length' => 40,
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
        'date_last_login' => array(
            'column_name' => 'date_last_login',
            'data_type' => 'int unsigned',
            'display' => 11,
            'column_default' => 0
        )
    );

}