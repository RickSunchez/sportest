<?php
namespace Shop\Commodity\Behaviors;

use Delorius\Behaviors\ORMBehavior;
use Delorius\Core\ORM;
use Shop\Commodity\Entity\Goods;

class ReviewBehavior extends ORMBehavior
{
    /** @var  int old parent id */
    protected $status;
    /** @var  bool  */
    protected $loaded;
    /** @var bool  */
    protected $isChanged;

    public function afterDelete(ORM $orm)
    {
        $goods = new Goods($orm->goods_id);
        if ( $goods->loaded() )
        {
            $this->minusRating( $orm, $goods );
        }
    }

    public function beforeSave(ORM $orm)
    {
        $original_values = $orm->original_values();
        $this->status = $original_values['status'];
        $this->isChanged = $orm->changed('status');
        $this->loaded = $orm->loaded();
    }

    public function afterSave(ORM $orm)
    {
        if( $this->loaded AND $this->isChanged )
        {
            $goods = new Goods($orm->goods_id);
            if( $orm->status == 1 )
            {
                $this->plusRating($orm, $goods);
            } else {
                $this->minusRating($orm, $goods );
            }
        }
        if ( ! $this->loaded AND $orm->status == 1 )
        {
            //$goods = new Goods($orm->goods_id);
            $this->plusRating($orm, new Goods($orm->goods_id));
        }
    }

    protected function plusRating ( $orm, $goods )
    {
        $goods->rating = ( $goods->rating * $goods->votes + $orm->rating ) / ($goods->votes + 1);
        $goods->votes++;
        $goods->save();
    }

    protected function minusRating ( $orm, $goods )
    {
        $goods->rating = ($goods->rating * $goods->votes) - $orm->rating;
        $goods->votes--;
        $goods->save();
    }
}