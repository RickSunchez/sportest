<?php
namespace Shop\Store\Component\Cart\Discount;

class WeekDiscount extends BaseDiscount {

    /**
     * 0-вс,1-пн,2-вт,3-ср,4-чт,5-пт,6-сб
     */


    /**
     * @return  bool
     */
    public function valid()
    {
        if(!$this->func){
            return false;
        }

        $weekId = date('w');
        $week = explode(',',$this->func);
        $result = array_search($weekId, $week);
        return is_int($result)?true:false;
    }

} 