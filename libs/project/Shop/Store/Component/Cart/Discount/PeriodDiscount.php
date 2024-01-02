<?php
namespace Shop\Store\Component\Cart\Discount;

class PeriodDiscount extends BaseDiscount
{

    /**
     * @return  bool
     */
    public function valid()
    {
        if (!$this->func)
            return false;

        list($start, $end) = explode('-', $this->func);
        $date_start = strtotime($start .'.'.  date('Y'));
        $date_end = strtotime($end .'.'. date('Y'));
        $date_current = strtotime(date('d.m.y'));

        if (
            $date_start <= $date_current &&
            $date_current <= $date_end
        ) {
            return true;
        } else {
            return false;
        }


    }

} 