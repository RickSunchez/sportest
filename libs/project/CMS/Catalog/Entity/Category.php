<?php
namespace CMS\Catalog\Entity;

use Delorius\Core\ORM;
use Delorius\Utils\Strings;

/**
 * Class Category
 * @package CMS\Catalog\Entity
 *
 * @property int $cid Primary key
 * @property int $external_id  External primary key
 * @property int $external_change External change (1- yes,0- no)
 * @property int $pid Parent primary key
 * @property int $type_id Type
 * @property string $name Name (max 200)
 * @property string $url Url (max 200)
 * @property string $description Description
 * @property int $children Count of children with parent
 * @property int $object Count of object
 * @property int $pos Position
 * @property int $status Status (1 - show, 0 - hide)
 */
class Category extends ORM
{

    const TYPE_NEWS = 1;
    const TYPE_GALLERY = 2;
    const TYPE_ARTICLE = 3;
    const TYPE_DOCS = 4;
    const TYPE_EVENT = 5;
    const TYPE_VIDEO = 6;

    /** @return array Types */
    public static function getTypes()
    {
        return array(
            self::TYPE_NEWS => 'Категории новостей',
            self::TYPE_GALLERY => 'Категории галерей',
            self::TYPE_ARTICLE => 'Категории статей',
            self::TYPE_DOCS => 'Категории документов',
            self::TYPE_EVENT => 'Категории событий',
            self::TYPE_VIDEO => 'Категории видео',
        );
    }

    /**
     * @return string
     */
    public function getNameType()
    {
        $types = self::getTypes();
        return $types[$this->type_id];
    }

    /**
     * @param $typeId
     * @return $this
     */
    public function type($typeId)
    {
        $this->where('type_id', '=', $typeId);
        return $this;
    }

    /**
     * @param null $id
     * @return Category
     */
    public function parent($id = null)
    {
        if ($id == null)
            $this->where('pid', '=', 0);
        else
            $this->where('pid', '=', $id);
        return $this;
    }

    /**
     * @param null $direction
     * @return Category
     */
    public function sort($direction = 'DESC')
    {
        $this->order_by('pos', $direction);
        return $this;
    }

    /**
     * @return Category
     */
    public function active()
    {
        $this->where('status', '=', 1);
        return $this;
    }

    public function behaviors()
    {
        return array(
            'helperCategoryBehavior' => 'CMS\Catalog\Behaviors\HelperCategoryBehavior',
            'editCategoryBehavior' => 'CMS\Catalog\Behaviors\EditCategoryBehavior',
            'metaDataBehavior' => array(
                'class' => 'CMS\Core\Behaviors\MetaDataBehavior',
                'title' => false
            ),
            'imageBehavior' => array(
                'class' => 'CMS\Core\Behaviors\ImageBehavior',
                'path' => 'category',
                'ratio_fill' => true,
                'background_color' => '#ffffff',
                'preview_width' => 250,
                'preview_height' => 250
            )
        );
    }

    protected $_primary_key = 'cid';
    protected $_table_name = 'df_category';
    protected $_table_columns_set = array('url', 'pid', 'type_id');

    protected function filters()
    {
        return array(
            TRUE => array(
                array('trim')
            ),
            'url' => array(
                array(array($this, 'translate'))
            ),
            'type_id' => array(
                array(array($this, 'setTypeId'))
            ),
        );
    }

    protected function rules()
    {
        return array(
            'pid' => array(
                array(array($this, 'checkParent'), array(':value'), 'К данной категории нельзя добавить дочерний каталог'),
            ),
            'name' => array(
                array('\\Delorius\\Utils\\Validators::isText', array(':value', 1, 200), 'Укажите название категории'),
            ),
        );
    }

    protected function setTypeId($value)
    {
        $types = self::getTypes();
        if (isset($types[$value])) {
            return $value;
        } else {
            return 0;
        }

    }

    protected function checkParent($value)
    {
        if (!$value)
            return true;

        $category = new self($value);
        if (!$category->loaded())
            return false;

        return true;
    }

    protected function translate($value)
    {
        if ($value == null) {
            $value = $this->name;
        }
        $str = Strings::webalize(Strings::translit(Strings::trim($value)));
        return $str;
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
        'cid' => array(
            'column_name' => 'cid',
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
        'pid' => array(
            'column_name' => 'pid',
            'data_type' => 'int unsigned',
            'display' => 11,
            'column_default' => 0
        ),
        'type_id' => array(
            'column_name' => 'type_id',
            'data_type' => 'int unsigned',
            'display' => 11,
            'column_default' => 0
        ),
        'name' => array(
            'column_name' => 'name',
            'data_type' => 'varchar',
            'character_maximum_length' => 200,
            'collation_name' => 'utf8_general_ci',
        ),
        'url' => array(
            'column_name' => 'url',
            'data_type' => 'varchar',
            'character_maximum_length' => 200,
            'collation_name' => 'utf8_general_ci',
        ),
        'description' => array(
            'column_name' => 'description',
            'data_type' => 'text',
            'collation_name' => 'utf8_general_ci',
        ),
        'children' => array(
            'column_name' => 'children',
            'data_type' => 'int unsigned',
            'display' => 11,
            'column_default' => 0
        ),
        'object' => array(
            'column_name' => 'object',
            'data_type' => 'int unsigned',
            'display' => 11,
            'column_default' => 0
        ),
        'pos' => array(
            'column_name' => 'pos',
            'data_type' => 'int unsigned',
            'display' => 11,
            'column_default' => 0
        ),
        'status' => array(
            'column_name' => 'status',
            'data_type' => 'tinyint',
            'display' => 1,
            'column_default' => 1
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