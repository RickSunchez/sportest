<?php
namespace Shop\Commodity\Entity;

use Delorius\Core\DateTime;
use Delorius\Core\ORM;

/**
 * Class Review
 * @package Shop\Commodity\Entity
 *
 * @property int $review_id Primary key
 * @property int $user_id User key
 * @property string $text Message
 * @property string $plus Good message
 * @property string $minus Bad message
 * @property int $rating Rating value
 * @property int $date_cr Date time created
 * @property int status Show review (1 - yes, 0 - no)
 */
class Review extends ORM
{

    /**
     * @param null $direction
     * @return $this
     */
    public function sort($direction = NULL){
        $this->order_created($direction);
        return $this;
    }

    /**
     * @return $this
     */
    public function active(){
        $this->where('status','=',1);
        return $this;
    }

    /**
     * @param $goods_id
     * @return $this
     */
    public function goods ( $goods_id )
    {
        $this->where('goods_id', '=', $goods_id);
        return $this;
    }


    public function as_array(){
        $arr = parent::as_array();
        $arr['created'] = DateTime::dateFormat($arr[$this->_created_column['column']],true);
        return $arr;
    }

    public function behaviors()
    {
        return array(
            'userBehavior' => 'CMS\Users\Behaviors\UserBehavior',
            'reviewBehavior' => 'Shop\Commodity\Behaviors\ReviewBehavior',
        );
    }

    protected function rules(){
        return array(
            'text'=>array(
                array('\\Delorius\\Utils\\Validators::isText', array(':value',1,50000),'Укажите Ваш отзыв'),
            ),
            'plus'=>array(
                array('\\Delorius\\Utils\\Validators::isText', array(':value',1,50000),'Укажите положительные качества товара'),
            ),
            'minus'=>array(
                array('\\Delorius\\Utils\\Validators::isText', array(':value',1,50000),'Укажите отрицательные качества товара'),
            ),
            'rating'=>array(
                array('\\Delorius\\Utils\\Validators::isText', array(':value',1,50000),'Укажите Вашу оценку товара'),
            ),
        );
    }

    protected $_primary_key = 'review_id';
    protected $_table_name = 'shop_goods_review';

    protected $_table_columns_set = array('user_id', 'goods_id', 'plus', 'minus', 'text', 'rating');

    protected $_created_column =  array(
        'column' => 'date_cr',
        'format' => TRUE,
    );

    protected $_table_columns = array(
        'review_id' => array(
            'column_name' => 'review_id',
            'data_type' => 'int unsigned',
            'display' => 11,
            'extra' => 'auto_increment',
            'key' => 'PRI'
        ),
        'goods_id' => array(
            'column_name' => 'goods_id',
            'data_type' => 'int unsigned',
            'display' => 11,
        ),
        'user_id' => array(
            'column_name' => 'user_id',
            'data_type' => 'tinyint unsigned',
            'display' => 1,
            'column_default' =>0,
        ),
        'text' => array(
            'column_name' => 'text',
            'data_type' => 'mediumtext',
            'collation_name' => 'utf8_general_ci',
        ),
        'minus' => array(
            'column_name' => 'minus',
            'data_type' => 'mediumtext',
            'collation_name' => 'utf8_general_ci',
        ),
        'plus' => array(
            'column_name' => 'plus',
            'data_type' => 'mediumtext',
            'collation_name' => 'utf8_general_ci',
        ),
        'date_cr' => array(
            'column_name' => 'date_cr',
            'data_type' => 'int unsigned',
            'display' => 11,
        ),
        'rating' => array(
            'column_name' => 'rating',
            'data_type' => 'int unsigned',
            'display' => 2,
        ),
        'status' => array(
            'column_name' => 'status',
            'data_type' => 'int unsigned',
            'display' => 1,
            'column_default' => 0,
        ),
    );
}