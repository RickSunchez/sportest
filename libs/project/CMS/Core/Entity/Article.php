<?php
namespace CMS\Core\Entity;

use Delorius\Core\DateTime;
use Delorius\Core\ORM;
use Delorius\Utils\Strings;
use Delorius\View\Html;

class Article extends ORM
{

    /**
     * @return $this
     */
    public function sort()
    {
        $this->order_created('desc')->order_pk();
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
     * @return string
     */
    public function link()
    {
        return link_to('article_show', array('id' => $this->pk(), 'url' => $this->url));
    }

    /**
     * @param int $max
     * @return mixed|string
     */
    public function getPreview($max = 55)
    {
        if ($this->preview) {
            return Strings::truncate($this->preview, $max);
        } else {
            return Strings::truncate(Html::clearTags($this->text), $max);
        }
    }

    public function as_array()
    {
        $arr = parent::as_array();
        $arr['created'] = DateTime::dateFormat($arr[$this->_created_column['column']], true);
        $arr['date_cr'] = $arr['date_cr'] ? date('d.m.Y H:i', $arr['date_cr']) : null;
        return $arr;
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
                array(array($this, 'setName'))
            ),
            'date_cr' => array(
                array(array($this, 'dateConvert'))
            )
        );
    }

    protected function rules()
    {
        return array(
            'text' => array(
                array('\\Delorius\\Utils\\Validators::isText', array(':value', 1, 50000), 'Введите текст статьи'),
            ),
            'name' => array(
                array('\\Delorius\\Utils\\Validators::isText', array(':value', 1, 200), 'Введите заголовок статьи'),
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

    protected function dateConvert($value)
    {
        if ($value == null) {
            return null;
        }
        return strtotime($value);
    }

    protected $_created_column = array(
        'column' => 'date_cr',
        'format' => TRUE,
    );

    protected $_updated_column = array(
        'column' => 'date_edit',
        'format' => TRUE,
    );

    protected function behaviors()
    {
        return array(
            'editObjectForCatalogBehavior' => 'CMS\Catalog\Behaviors\EditObjectForCatalogBehavior',
            'tagsBehavior' => 'CMS\Core\Behaviors\TagsBehavior',
            'metaDataBehavior' => array(
                'class' => 'CMS\Core\Behaviors\MetaDataBehavior',
                'desc' => '{preview}',
            ),
            'imageBehavior' => array(
                'class' => 'CMS\Core\Behaviors\ImageBehavior',
                'path' => 'article',
                'crop' => true,
                'preview_width' => 200,
                'preview_height' => 200,
            ),
        );
    }

    protected $_table_name = 'df_article';
    protected $_table_columns_set = array('text', 'name', 'url', 'preview');

    protected $_table_columns = array(
        'id' => array(
            'column_name' => 'id',
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
        'cid' => array(
            'column_name' => 'cid',
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
            'character_maximum_length' => 200,
            'collation_name' => 'utf8_general_ci',
        ),
        'source' => array(
            'column_name' => 'source',
            'data_type' => 'varchar',
            'character_maximum_length' => 200,
            'collation_name' => 'utf8_general_ci',
        ),
        'preview' => array(
            'column_name' => 'preview',
            'data_type' => 'varchar',
            'character_maximum_length' => 1000,
            'collation_name' => 'utf8_general_ci',
        ),
        'text' => array(
            'column_name' => 'text',
            'data_type' => 'mediumtext',
            'collation_name' => 'utf8_general_ci',
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
        ),

    );

}