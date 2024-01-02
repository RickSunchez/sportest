<?php
namespace CMS\Core\Entity;

use CMS\Core\Helper\Helpers;
use Delorius\Core\ORM;
use Delorius\DataBase\DB;


/**
 * Class Image
 * @package CMS\Core\Entity
 *
 * @property int $image_id Primary key
 * @property int $target_id Target object id
 * @property string $target_type Target object type
 * @property int $horizontal Image is horizontally (1 - yes,0 - no)
 * @property int $main Image is main (1 - yes,0 - no)
 * @property int $width  Width (px)
 * @property int $height  Height (px)
 * @property int $pre_width  Width preview (px)
 * @property int $pre_height  Height preview (px)
 * @property string $normal Path by normal size
 * @property string $preview Path by preview size
 * @property string $name Name
 * @property int $pos Position
 * @property int $date_cr Date time created
 */
class Image extends ORM
{
    /** @return $this */
    public function whereByTargetType(ORM $orm)
    {
        $this->where('target_type', '=', Helpers::getTableId($orm));
        return $this;
    }

    /** @return $this */
    public function whereByTargetId($targetId)
    {
        if (is_array($targetId)) {
            $this->where('target_id', 'IN', $targetId);
        } else {
            $this->where('target_id', '=', $targetId);
        }
        return $this;
    }

    /**
     * @param null $direction
     * @return $this
     */
    public function sort($direction = 'DESC')
    {
        $this->order_by('main', 'desc')->order_by('pos', $direction)->order_pk();
        return $this;
    }

    /**
     * @return $this
     */
    public function main($main = false)
    {
        if ($main) {
            $this->where('main', '=', 1);
        } else {
            $this->group_by('image_id');
        }
        return $this;
    }

    /**
     * @param $status
     */
    public function setMainStatus($status = 1)
    {
        if ($status == 1) {
            DB::update($this->table_name())
                ->set(array('main' => 0))
                ->where('target_type', '=', $this->target_type)
                ->where('target_id', '=', $this->target_id)
                ->where('main', '=', 1)
                ->execute($this->_db);
        }

        $this->main = $status ? 1 : 0;
    }

    /** bool */
    public function existsFile($value)
    {
        return file_exists(DIR_INDEX . $value) ? true : false;
    }

    protected function behaviors()
    {
        return array(
            'editImageBehavior' => 'CMS\Core\Behaviors\EditImageBehavior',
        );
    }

    protected function rules()
    {
        return array(
            'normal' => array(
                array(array($this, 'existsFile'), array(':value'), 'Файла не существует.'),
            ),
            'preview' => array(
                array(array($this, 'existsFile'), array(':value'), 'Превьюшьки нет.'),
            )
        );
    }

    public function as_array()
    {
        $arr = parent::as_array();
        $arr['basename'] = basename($this->normal);
        return $arr;
    }

    protected $_primary_key = 'image_id';
    protected $_table_name = 'df_images';

    protected $_table_columns_set = array('normal', 'preview', 'width', 'height', 'horizontal', 'target_type', 'target_id');

    protected $_created_column = array(
        'column' => 'date_cr',
        'format' => TRUE,
    );

    protected $_table_columns = array(
        'image_id' => array(
            'column_name' => 'image_id',
            'data_type' => 'int unsigned',
            'display' => 11,
            'extra' => 'auto_increment',
            'key' => 'PRI'
        ),
        'target_id' => array(
            'column_name' => 'target_id',
            'data_type' => 'int unsigned',
            'display' => 11,
        ),
        'target_type' => array(
            'column_name' => 'target_type',
            'data_type' => 'int unsigned',
            'display' => 11,
        ),
        'horizontal' => array(
            'column_name' => 'horizontal',
            'data_type' => 'tinyint unsigned',
            'display' => 1,
            'column_default' => 0
        ),
        'main' => array(
            'column_name' => 'main',
            'data_type' => 'tinyint unsigned',
            'display' => 1,
            'column_default' => 0
        ),
        'width' => array(
            'column_name' => 'width',
            'data_type' => 'int unsigned',
            'display' => 11,
            'column_default' => 0
        ),
        'height' => array(
            'column_name' => 'height',
            'data_type' => 'int unsigned',
            'display' => 11,
            'column_default' => 0
        ),
        'pre_width' => array(
            'column_name' => 'pre_width',
            'data_type' => 'int unsigned',
            'display' => 11,
            'column_default' => 0
        ),
        'pre_height' => array(
            'column_name' => 'pre_height',
            'data_type' => 'int unsigned',
            'display' => 11,
            'column_default' => 0
        ),
        'normal' => array(
            'column_name' => 'normal',
            'data_type' => 'varchar',
            'character_maximum_length' => 200,
            'collation_name' => 'utf8_general_ci',
        ),
        'preview' => array(
            'column_name' => 'preview',
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
        'date_cr' => array(
            'column_name' => 'date_cr',
            'data_type' => 'int unsigned',
            'display' => 11,
        ),
    );
}