<?php
namespace CMS\Core\Entity;

use Delorius\Core\ORM;
use Delorius\Utils\Strings;

class Gallery extends ORM
{

    /**
     * @return string
     */
    public function link()
    {
        return link_to('gallery', array('id' => $this->pk()));
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
     * @param string $direction
     * @return $this
     */
    public function sort($direction = 'desc')
    {
        $this->order_by('pos', $direction)->order_pk('desc');
        return $this;
    }

    /**
     * @param int $max
     * @return mixed|string
     */
    public function getPreview($max = 55)
    {
        return Strings::truncate($this->note, $max);
    }

    protected function behaviors()
    {
        return array(
            'editObjectForCatalogBehavior' => 'CMS\Catalog\Behaviors\EditObjectForCatalogBehavior',
            'galleryBehavior' => array(
                'class' => 'CMS\Core\Behaviors\GalleryBehavior',
                'path' => 'gallery',
                'crop' => true,
                'preview_width' => 300,
                'preview_height' => 300
            ),
        );
    }

    protected $_created_column = array(
        'column' => 'date_cr',
        'format' => TRUE,
    );

    protected $_primary_key = 'gallery_id';
    protected $_table_name = 'df_gallery';

    protected $_table_columns = array(
        'gallery_id' => array(
            'column_name' => 'gallery_id',
            'data_type' => 'int unsigned',
            'display' => 11,
            'extra' => 'auto_increment',
            'key' => 'PRI'
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
        'cid' => array(
            'column_name' => 'cid',
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
        'name' => array(
            'column_name' => 'name',
            'data_type' => 'varchar',
            'character_maximum_length' => 200,
            'collation_name' => 'utf8_general_ci',
        ),
        'note' => array(
            'column_name' => 'note',
            'data_type' => 'varchar',
            'character_maximum_length' => 1000,
            'collation_name' => 'utf8_general_ci',
        ),
        'date_cr' => array(
            'column_name' => 'date_cr',
            'data_type' => 'int unsigned',
            'display' => 11,
        ),
    );
}