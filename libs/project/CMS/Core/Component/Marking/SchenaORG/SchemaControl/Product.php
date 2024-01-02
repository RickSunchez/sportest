<?php
namespace CMS\Core\Component\Marking\SchenaORG\SchemaControl;

class Product extends BaseControl
{
    protected $type = 'http://schema.org/Product';

    /**
     * @param $price
     * @param null $currency
     * @param bool $availability
     * @return $this
     */
    public function addOffer($price, $currency = null, $availability = false, $categories = null)
    {
        $Offer = $this->prop('offers')->setTag('div', true)
            ->scope('Offer');
        $Offer->prop('price', $price);
        if ($currency != null) {
            $Offer->prop('priceCurrency', $currency);
        }
        if ($availability) {
            $Offer->prop('availability', 'В наличии', array('href' => 'http://schema.org/InStock'))
                ->setTag('link');
        }
        if ($categories != null) {
            $Offer->prop('category', $categories);
        }

        return $this;
    }

} 