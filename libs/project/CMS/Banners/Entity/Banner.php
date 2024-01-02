<?php
namespace CMS\Banners\Entity;

use Delorius\Core\ORM;

/**
 * Class Banner
 * @package CMS\Banners\Entity
 *
 * @property int $banner_id Primary key
 * @property int $type_id Type banner
 * @property string $code Code banner
 * @property string $name Title banner
 * @property string $url Address http
 * @property int $visit Count hits
 * @property int $click Count click by banner
 * @property int $redirect Redirect by url (1 - yes, 0- no)
 * @property int $status Status banner (1 - show, 0 - hide)
 * @property int $width Width banner (px)
 * @property int $height Height banner (px)
 * @property string $html HTML code banner
 * @property string $path Path by files (image or flash)
 * @property int $date_show_up Date time show banner
 */
class Banner extends ORM
{

    /**
     * @return string
     */
    public function link()
    {
        if ($this->redirect) {
            return link_to('banner_go', array('id' => $this->pk()));
        } else {
            return $this->url;
        }
    }

    /**
     * @return $this
     */
    public function sort()
    {
        $this->order_by('pos', 'desc')->order_pk();
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
     * @param $code
     * @return $this
     */
    public function whereByCode($code)
    {
        $this->where('code', '=', $code);
        return $this;
    }

    const TYPE_HTML = 1;
    const TYPE_IMAGE = 2;
    const TYPE_FLASH = 3;

    /** @return array Types */
    public static function getTypes()
    {
        return array(
            self::TYPE_HTML => 'HTML код',
            self::TYPE_IMAGE => 'Изображение',
            self::TYPE_FLASH => 'Flash',
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

    protected function behaviors()
    {
        return array(
            'editBannerBehavior' => 'CMS\Banners\Behaviors\EditBannerBehavior',
        );
    }

    public function as_array()
    {
        $arr = parent::as_array();
        $arr['type_name'] = $this->getNameType();
        $arr['width'] = $arr['width'] ? $arr['width'] : null;
        $arr['height'] = $arr['height'] ? $arr['height'] : null;
        $arr['date_show_up'] = $arr['date_show_up'] ? date('d.m.Y H:i', $arr['date_show_up']) : null;
        return $arr;
    }

    protected function filters()
    {
        return array(
            TRUE => array(
                array('trim')
            ),
            'date_show_up' => array(
                array(array($this, 'dateConvert'))
            )
        );
    }

    protected function dateConvert($value)
    {
        if ($value == null) {
            return null;
        }
        return strtotime($value);
    }

    protected $_primary_key = 'banner_id';
    protected $_table_name = 'df_banners';

    protected $_table_columns_set = array();

    public function rules()
    {
        return array(
            'code' => array(
                array('\\Delorius\\Utils\\Validators::isText', array(':value', 1, 50), 'Код баннера должен быть длиной от 1 до 50 символов'),
            ),
            'name' => array(
                array('\\Delorius\\Utils\\Validators::isText', array(':value', 1, 50), 'Заголовок банера долен быть от 1 до 50 символов'),
            ),
        );
    }

    protected $_table_columns = array(
        'banner_id' => array(
            'data_type' => 'int unsigned',
            'display' => 11,
            'column_name' => 'banner_id',
            'extra' => 'auto_increment',
            'key' => 'PRI'
        ),
        'type_id' => array(
            'column_name' => 'type_id',
            'data_type' => 'int unsigned',
            'display' => 11,
            'column_default' => self::TYPE_HTML
        ),
        'code' => array(
            'column_name' => 'code',
            'data_type' => 'varchar',
            'character_maximum_length' => 50,
            'collation_name' => 'utf8_general_ci',
        ),
        'name' => array(
            'column_name' => 'name',
            'data_type' => 'varchar',
            'character_maximum_length' => 50,
            'collation_name' => 'utf8_general_ci',
        ),
        'url' => array(
            'column_name' => 'url',
            'data_type' => 'varchar',
            'character_maximum_length' => 100,
            'collation_name' => 'utf8_general_ci',
        ),
        'visit' => array(
            'column_name' => 'visit',
            'data_type' => 'int unsigned',
            'display' => 11,
            'column_default' => 0
        ),
        'click' => array(
            'column_name' => 'click',
            'data_type' => 'int unsigned',
            'display' => 11,
            'column_default' => 0
        ),
        'redirect' => array(
            'column_name' => 'redirect',
            'data_type' => 'tinyint unsigned',
            'display' => 1,
            'column_default' => 0
        ),
        'status' => array(
            'column_name' => 'status',
            'data_type' => 'tinyint unsigned',
            'display' => 1,
            'column_default' => 0,
        ),
        'width' => array(
            'column_name' => 'width',
            'data_type' => 'int unsigned',
            'display' => 11,
            'column_default' => 0,
        ),
        'height' => array(
            'column_name' => 'height',
            'data_type' => 'int unsigned',
            'display' => 11,
            'column_default' => 0,
        ),
        'html' => array(
            'column_name' => 'html',
            'data_type' => 'text',
            'collation_name' => 'utf8_general_ci',
        ),
        'path' => array(
            'column_name' => 'path',
            'data_type' => 'varchar',
            'character_maximum_length' => 300,
            'collation_name' => 'utf8_general_ci',
        ),
        'date_show_up' => array(
            'column_name' => 'date_show_up',
            'data_type' => 'int',
            'display' => 11,
            'column_default' => 0,
        ),
        'pos' => array(
            'column_name' => 'pos',
            'data_type' => 'int',
            'display' => 11,
            'column_default' => 0,
        ),

    );
}