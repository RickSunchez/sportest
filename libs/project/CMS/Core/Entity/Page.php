<?php
namespace CMS\Core\Entity;

use Delorius\Core\ORM;
use Delorius\DataBase\DB;
use Delorius\Utils\Strings;
use Delorius\View\Html;

class Page extends ORM
{

    /**
     * @param int $max
     * @return mixed|string
     */
    public function getPreview($max = 55)
    {
        return Strings::truncate(Html::clearTags($this->text), $max);
    }


    public function as_array()
    {
        $arr = parent::as_array();
        $arr['link'] = $this->link();
        return $arr;
    }

    protected $_table_name = 'df_pages';
    protected $_table_columns_set = array('title', 'short_title', 'text', 'template_page', 'template_dir', 'url');

    protected function filters()
    {
        return array(
            TRUE => array(
                array('trim')
            ),
            'url' => array(
                array(array($this, 'translate'))
            ),
            'title' => array(
                array(array($this, 'setTitle'))
            )
        );
    }

    public function rules()
    {
        return array(
            'template_page' => array(
                array('\\Delorius\\Utils\\Validators::isText', array(':value'), 'Выберите шаблон страницы'),
            ),
            'template_dir' => array(
                array('\\Delorius\\Utils\\Validators::isText', array(':value'), 'Выберите категорию шаблона'),
            ),
            'title' => array(
                array('\\Delorius\\Utils\\Validators::isText', array(':value'), 'Введите заголовок страницы'),
            ),
            'short_title' => array(
                array('\\Delorius\\Utils\\Validators::isText', array(':value', 1, 800), 'Введите короткий заголовок страницы'),
            ),
            'text' => array(
                array('\\Delorius\\Utils\\Validators::isText', array(':value', 0, 1000000), 'Введите текст страницы'),
            ),
        );
    }

    /**
     * @param $site
     * @return $this
     */
    public function site($site)
    {
        $this->where('site', '=', $site);
        return $this;
    }

    /** @return $this */
    public function main($status = 1)
    {
        $this->where('main', '=', $status);
        return $this;
    }

    /**
     * @return $this
     */
    public function active()
    {
        $this->where('status', '=', 1);
        return $this;
    }

    /**
     * @param null $direction
     * @return $this
     */
    public function sort($direction = 'DESC')
    {
        $this->order_by('pos', $direction)->order_pk();
        return $this;
    }

    /**
     * @param $status
     */
    public function setMain($status)
    {
        DB::update($this->table_name())
            ->set(array('main' => 0))
            ->where('site', '=', $this->site)
            ->where('main', '=', 1)
            ->execute($this->_db);

        $this->main = $status ? 1 : 0;
    }

    protected function setTitle($value)
    {
        $url = $value;
        if ($this->url != null) {
            $url = $this->url;
        }
        $this->url = Strings::webalize(Strings::translit(Strings::trim($url)));
        return $value;
    }

    protected function translate($value)
    {
        if ($value == null) {
            $value = $this->title;
        }
        $str = Strings::webalize(Strings::translit($value));
        if ($this->unique('url', $str)) {
            return $str;
        }
        return $this->translate($str . 'x');
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
        'pid' => array(
            'column_name' => 'pid',
            'data_type' => 'int unsigned',
            'display' => 11,
            'column_default' => 0,
        ),
        'site' => array(
            'column_name' => 'site',
            'data_type' => 'varchar',
            'column_default' => 'www',
            'character_maximum_length' => 20,
            'collation_name' => 'utf8_general_ci',
        ),
        'url' => array(
            'column_name' => 'url',
            'data_type' => 'varchar',
            'character_maximum_length' => 400,
            'collation_name' => 'utf8_general_ci',
        ),
        'title' => array(
            'column_name' => 'title',
            'data_type' => 'varchar',
            'character_maximum_length' => 400,
            'collation_name' => 'utf8_general_ci',
        ),
        'short_title' => array(
            'column_name' => 'short_title',
            'data_type' => 'varchar',
            'character_maximum_length' => 800,
            'collation_name' => 'utf8_general_ci',
        ),
        'keys' => array(
            'column_name' => 'keys',
            'data_type' => 'varchar',
            'character_maximum_length' => 800,
            'collation_name' => 'utf8_general_ci',
        ),
        'description' => array(
            'column_name' => 'description',
            'data_type' => 'varchar',
            'character_maximum_length' => 800,
            'collation_name' => 'utf8_general_ci',
        ),
        'text' => array(
            'column_name' => 'text',
            'data_type' => 'mediumtext',
            'collation_name' => 'utf8_general_ci',
        ),
        'last_edit' => array(
            'column_name' => 'last_edit',
            'data_type' => 'int',
            'display' => 11,
        ),
        'template_page' => array(
            'column_name' => 'template_page',
            'data_type' => 'varchar',
            'character_maximum_length' => 100,
            'collation_name' => 'utf8_general_ci',
        ),
        'mobile' => array(
            'column_name' => 'mobile',
            'data_type' => 'varchar',
            'character_maximum_length' => 100,
            'collation_name' => 'utf8_general_ci',
        ),
        'prefix' => array(
            'column_name' => 'prefix',
            'data_type' => 'varchar',
            'character_maximum_length' => 50,
            'collation_name' => 'utf8_general_ci',
        ),
        'template_dir' => array(
            'column_name' => 'template_dir',
            'data_type' => 'varchar',
            'character_maximum_length' => 100,
            'collation_name' => 'utf8_general_ci',
        ),
        'pos' => array(
            'column_name' => 'pos',
            'data_type' => 'int unsigned',
            'display' => 11,
            'column_default' => 0,
        ),
        'main' => array(
            'column_name' => 'main',
            'data_type' => 'tinyint unsigned',
            'display' => 1,
            'column_default' => 0,
        ),
        'status' => array(
            'column_name' => 'status',
            'data_type' => 'tinyint unsigned',
            'display' => 1,
            'column_default' => 1,
        ),
        'redirect' => array(
            'column_name' => 'redirect',
            'data_type' => 'varchar',
            'character_maximum_length' => 400,
            'collation_name' => 'utf8_general_ci',
        ),
        'children' => array(
            'column_name' => 'children',
            'data_type' => 'int unsigned',
            'display' => 11,
            'column_default' => 0
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
    );

}