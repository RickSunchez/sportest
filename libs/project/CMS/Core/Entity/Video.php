<?php
namespace CMS\Core\Entity;

use Delorius\Core\DateTime;
use Delorius\Core\Environment;
use Delorius\Core\ORM;
use Delorius\Utils\Strings;
use Delorius\View\Html;
use RicardoFiorani\Matcher\VideoServiceMatcher;

class Video extends ORM
{
    /**
     * @return string
     */
    public function link()
    {
        $domainRouter = Environment::getContext()
            ->getService('domainRouter');
        $host = $domainRouter->generate($this->site);
        return $host . link_to('video', array('id' => $this->pk()));
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
    public function main()
    {
        $this->where('main', '=', 1);
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
     * @return $this
     */
    public function sort(){
        $this->order_by('pos','desc')
            ->order_pk('desc');
        return $this;
    }

    /**
     * @param int $max
     * @return string
     */
    public function getPreview($max = 55)
    {
        return Strings::truncate(Html::clearTags($this->text), $max);
    }

    public function as_array()
    {
        $arr = parent::as_array();
        $arr['created'] = DateTime::dateFormat($arr['date_cr'], true);
        $arr['date_edit'] = $arr['date_edit'] ? date('d.m.Y H:i', $arr['date_edit']) : null;
        return $arr;
    }

    protected function behaviors()
    {
        return array(
            'helpVideoBehavior' => 'CMS\Core\Behaviors\HelpVideoBehavior',
            'editObjectForCatalogBehavior' => 'CMS\Catalog\Behaviors\EditObjectForCatalogBehavior',
            'tagsBehavior' => 'CMS\Core\Behaviors\TagsBehavior',
            'metaDataBehavior' => array(
                'class' => 'CMS\Core\Behaviors\MetaDataBehavior',
                'desc' => '{text}',
            ),
            'imageBehavior' => array(
                'class' => 'CMS\Core\Behaviors\ImageBehavior',
                'path' => 'video',
                'crop' => true,
                'preview_width' => 220,
                'preview_height' => 120,
            ),
        );
    }

    protected function rules()
    {
        return array(
            'url' => array(
                array(array($this, 'embeddable'), array(':value'), 'Видео не доступно'),
            )
        );
    }

    protected function embeddable($value)
    {
        if(!$value)
             return false;

        return get(new VideoServiceMatcher())
            ->parse($value)
            ->isEmbeddable();
    }

    protected $_table_name = 'df_videos';
    protected $_table_columns_set = array('url');

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
        'status' => array(
            'column_name' => 'status',
            'data_type' => 'tinyint unsigned',
            'display' => 1,
            'column_default' => 0,
        ),
        'main' => array(
            'column_name' => 'main',
            'data_type' => 'tinyint unsigned',
            'display' => 1,
            'column_default' => 0,
        ),
        'cid' => array(
            'column_name' => 'cid',
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