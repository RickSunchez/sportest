<?php
namespace Shop\Commodity\Controller;

use CMS\Core\Entity\Image;
use Delorius\Exception\Error;
use Delorius\Utils\Arrays;
use Shop\Commodity\Entity\Goods;
use Shop\Commodity\Entity\Options\Item;
use Shop\Commodity\Entity\Options\Variant;
use Shop\Commodity\Helpers\Options;

class OptionController extends GoodsController
{

    /**
     * @var \Shop\Store\Model\CurrencyBuilder
     * @service currency
     * @inject
     */
    public $currency;

    /**
     * @Post
     * @return [goods],[additions],[price]
     */
    public function changedDataAction()
    {
        /**
         * $product_data = [
         *  [goods] = [goods_id,amount,options[option=>value..]]
         *  [additions] = [goods_id,amount,options[option=>value..]] ..
         *  [required] = [field1,filed2]] ..
         *  [image] = bool / default = false..
         * ]
         */
        $product_data = $this->httpRequest->getRequest('product_data', array());

        try {

            $goods = new Goods($product_data['goods']['goods_id']);
            if (!$goods->loaded()) {
                throw new Error('Не указан товар');
            }
            Options::accept($goods, $product_data['goods']['options'], $product_data['goods']['image']);
            $result['ok'] = 1;

            $required = $product_data['goods']['required'];
            $result['goods'] = Arrays::filterByValueKey($goods->as_array(), function ($value, $key) use ($required) {
                return Arrays::search($required, $key) === false ? false : true;
            });

            $value_goods = $goods->value;
            $current_code = $goods->code;
            $value_system = 0;
            #additions calc
            if (count($product_data['additions'])) {
                $result['additions'] = array();
                foreach ($product_data['additions'] as $addition) {
                    $item = new Goods($addition['goods_id']);
                    if ($item->loaded()) {
                        Options::accept($item, $addition['options'], $addition['image']);
                        $value = $this->currency->convert($item->value, $item->code, $current_code);
                        $value_system += $value;
                        $arr = $item->as_array();
                        $arr['value'] = $value;
                        $arr['price'] = $this->currency->format($value, $current_code);
                        $arr['price_raw'] = $this->currency->format($value, $current_code, null, false);


                        $required = $addition['required'];
                        $arr = Arrays::filterByValueKey($arr, function ($value, $key) use ($required) {
                            return Arrays::search($required, $key) === false ? false : true;
                        });

                        $result['additions'][$item->pk()] = $arr;
                    }
                }

                $result['price']['additions']['value'] = $value_system;
                $result['price']['additions']['all'] = $this->currency->format($value_system, $current_code);
                $result['price']['additions']['all_raw'] = $this->currency->format($value_system, $current_code, null, false);

            }

            $all = $value_goods + $value_system;
            $result['price']['goods']['value'] = $all;
            $result['price']['goods']['all'] = $this->currency->format($all, $current_code);
            $result['price']['goods']['all_raw'] = $this->currency->format($all, $current_code, null, false);


        } catch (Error $e) {
            $result['error'] = $e->getMessage();
        }
        $this->response($result);

    }


    /**
     * @Post
     */
    public function infoDataAction()
    {
        $variant_id = $this->httpRequest->getRequest('variant_id');

        try {
            $variant = new Variant($variant_id);
            if (!$variant->loaded()) {
                throw new Error('Не существует данного варианта');
            }

            $option = new Item($variant->option_id);
            $image = $variant->getImage();
            $html = $this->view->load('shop/options/show', array(
                'variant' => $variant,
                'option' => $option,
                'image' => $image,
            ));

            $result['html'] = $html;

        } catch (Error $e) {
            $result['error'] = $e->getMessage();
        }

        $this->response($result);

    }


    public function listPartial($goods_id, $code = SYSTEM_CURRENCY, $is_array = false, $theme = null)
    {
        $var['code'] = $code;
        $var['goods_id'] = $goods_id;
        $items = Item::model()
            ->byGoodsId($goods_id)
            ->active()
            ->sort()
            ->find_all();

        $ids = array();
        foreach ($items as $item) {
            $ids[] = $item->pk();
            $var['options'][] = $is_array ? $item->as_array() : $item;
        }

        if (count($ids)) {
            $variants = Variant::model()
                ->byGoodsId($goods_id)
                ->active()
                ->sort()
                ->where('option_id', 'in', $ids)
                ->find_all();

            foreach ($variants as $variant) {
                $var['variants'][$variant->option_id][] = $is_array ? $variant->as_array() : $variant;
            }

        }
        $theme = $theme ? '_' . $theme : '';
        $this->response($this->view->load($this->config['view']['option'] . '/index' . $theme, $var));
    }

    public function selectPartial($option, $code, $variants, $theme = null)
    {
        $var['code'] = $code;
        $var['variants'] = $variants;
        $var['option'] = $option;
        $theme = $theme ? '_' . $theme : '';
        $this->response($this->view->load($this->config['view']['option'] . '/_select' . $theme, $var));
    }

    public function radioPartial($option, $code, $variants, $theme = null, $image = false)
    {
        if ($image) {
            $ids = array();
            foreach ($variants as $variant) {
                $ids[] = is_array($variant) ? $variant['id'] : $variant->pk();
            }

            if (count($ids)) {
                $images = Image::model()
                    ->whereByTargetType(Variant::model())
                    ->whereByTargetId($ids)
                    ->find_all();
                $var['images'] = Arrays::resultAsArrayKey($images, 'target_id');
            }
        }

        $var['code'] = $code;
        $var['variants'] = $variants;
        $var['option'] = $option;
        $theme = $theme ? '_' . $theme : '';
        $this->response($this->view->load($this->config['view']['option'] . '/_radio' . $theme, $var));
    }

    public function flagPartial($option, $code, $variants, $theme = null)
    {
        $var['code'] = $code;
        $var['variants'] = $variants;
        $var['option'] = $option;
        $theme = $theme ? '_' . $theme : '';
        $this->response($this->view->load($this->config['view']['option'] . '/_flag' . $theme, $var));
    }

    public function textPartial($option, $variants, $theme = null)
    {
        $var['variants'] = $variants;
        $var['option'] = $option;
        $theme = $theme ? '_' . $theme : '';
        $this->response($this->view->load($this->config['view']['option'] . '/_text' . $theme, $var));
    }

    public function varcharPartial($option, $variants, $theme = null)
    {
        $var['variants'] = $variants;
        $var['option'] = $option;
        $theme = $theme ? '_' . $theme : '';
        $this->response($this->view->load($this->config['view']['option'] . '/_varchar' . $theme, $var));
    }

}