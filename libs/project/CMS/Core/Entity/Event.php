<?php
namespace CMS\Core\Entity;

use Delorius\Core\DateTime;
use Delorius\Core\Environment;
use Delorius\Core\ORM;
use Delorius\Utils\Strings;
use Delorius\View\Html;

/**
 * Class Event
 * @package CMS\Core\Entity
 *
 * @property int $id Primary key
 * @property int $type_id Type news
 * @property string $name Title news
 * @property int $cid Category id
 * @property int $main Main news (1 - yse, 0 - no)
 * @property int $status Status news (1 - show, 0 hide)
 * @property string $site By domain (max=20)
 * @property string $url By path (max=200)
 * @property string $preview Brief about news (max=200)
 * @property string $text About news
 * @property int $date_cr Date time created
 * @property string $source Source link
 * @property int $gallery_id Gallery Id
 */
class Event extends ORM
{

    /**
     * @return int
     */
    public function daysLeftEnd()
    {
        if (!$this->date_end) {
            return 0;
        }
        return $this->getDaysLeft($this->date_end);
    }

    /**
     * @return int
     */
    public function daysLeftStart()
    {
        if (!$this->date_cr) {
            return 0;
        }
        return $this->getDaysLeft($this->date_cr);
    }



    /**
     * @return string
     */
    public function link()
    {
        $domainRouter = Environment::getContext()
            ->getService('domainRouter');
        $host = $domainRouter->generate($this->site);
        return $host . link_to('event_show', array('id' => $this->pk(), 'url' => $this->url));
    }

    /**
     * @return $this
     */
    public function sort()
    {
        $this->order_by('main','DESC')
            ->order_by('date_cr')
            ->order_pk();
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
     * @return $this
     */
    public function not_final()
    {
        $time = time();
        $this->where('date_end', '>=', $time);
        return $this;
    }

    /**
     * @return $this
     */
    public function main()
    {
        $this->where('main', '=', 1);
        return $this;
    }

    /**
     * @return $this
     */
    public function notMain()
    {
        $this->where('main', '=', 0);
        return $this;
    }

    /**
     * @param string $domain
     * @return $this
     */
    public function site($domain = 'www')
    {
        $this->where('site', '=', $domain);
        return $this;
    }

    /**
     * @param int $max
     * @return string
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
        $arr['created'] = DateTime::dateFormat($arr['date_cr'], true);
        $arr['date_cr'] = $arr['date_cr'] ? date('d.m.Y H:i', $arr['date_cr']) : null;
        $arr['ended'] = DateTime::dateFormat($arr['date_end'], true);
        $arr['date_end'] = $arr['date_end'] ? date('d.m.Y H:i', $arr['date_end']) : null;
        return $arr;
    }

    protected $_table_name = 'df_event';
    protected $_table_columns_set = array('text', 'name', 'url', 'preview', 'date_cr');

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
            'preview' => array(
                array('\Delorius\Utils\Strings::truncate', array(':value', 200))
            ),
            'date_cr' => array(
                array(array($this, 'dateConvert'))
            ),
            'date_end' => array(
                array(array($this, 'dateConvert'))
            )
        );
    }

    protected function rules()
    {
        return array(
            'name' => array(
                array('\\Delorius\\Utils\\Validators::isText', array(':value', 1, 500), 'Введите заголовок события'),
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
            return time();
        }
        if (preg_match('/\D/', $value) == 1) {
            return strtotime($value);
        }
        return $value;
    }

    protected function getDaysLeft($date){
        if (!$date) {
            return 0;
        }
        $now = strtotime(date('d.m.Y', time()));
        $date_end = strtotime(date('d.m.Y', $date));

        if ($date < $now) {
            return -1;
        }
        $interval = $date_end - $now;
        if ($interval == 0) {
            return 0;
        }
        $days = ($interval / 86400) + 1;
        return (int)$days;
    }

    protected function behaviors()
    {
        return array(
            'editObjectForCatalogBehavior' => 'CMS\Catalog\Behaviors\EditObjectForCatalogBehavior',
            'metaDataBehavior' => array(
                'class' => 'CMS\Core\Behaviors\MetaDataBehavior',
                'desc' => '{text}',
            ),
            'imageBehavior' => array(
                'class' => 'CMS\Core\Behaviors\ImageBehavior',
                'path' => 'event',
                'crop' => true,
                'preview_width' => 200,
                'preview_height' => 200,
            ),
        );
    }

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
            'character_maximum_length' => 500,
            'collation_name' => 'utf8_general_ci',
        ),
        'cid' => array(
            'column_name' => 'cid',
            'data_type' => 'int unsigned',
            'display' => 11,
            'column_default' => 0,
        ),
        'main' => array(
            'column_name' => 'main',
            'data_type' => 'int unsigned',
            'display' => 1,
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
            'character_maximum_length' => 500,
            'collation_name' => 'utf8_general_ci',
        ),
        'preview' => array(
            'column_name' => 'preview',
            'data_type' => 'varchar',
            'character_maximum_length' => 500,
            'collation_name' => 'utf8_general_ci',
        ),
        'text' => array(
            'column_name' => 'text',
            'data_type' => 'mediumtext',
            'collation_name' => 'utf8_general_ci',
        ),
        'location' => array(
            'column_name' => 'location',
            'data_type' => 'varchar',
            'character_maximum_length' => 1000,
            'collation_name' => 'utf8_general_ci',
        ),
        'location_name' => array(
            'column_name' => 'location_name',
            'data_type' => 'varchar',
            'character_maximum_length' => 1000,
            'collation_name' => 'utf8_general_ci',
        ),
        'date_cr' => array(
            'column_name' => 'date_cr',
            'data_type' => 'int unsigned',
            'display' => 11,
        ),
        'date_end' => array(
            'column_name' => 'date_end',
            'data_type' => 'int unsigned',
            'display' => 11,
            'column_default' => 0
        ),
        'prefix' => array(
            'column_name' => 'prefix',
            'data_type' => 'varchar',
            'character_maximum_length' => 50,
            'collation_name' => 'utf8_general_ci',
        ),
    );
}