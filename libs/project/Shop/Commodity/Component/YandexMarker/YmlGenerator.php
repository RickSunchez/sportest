<?php

namespace Shop\Commodity\Component\YandexMarker;

use CMS\Core\Helper\ParserString;
use Delorius\Core\Environment;
use Delorius\Core\Object;
use Delorius\Utils\Arrays;
use Delorius\Utils\FileSystem;
use Delorius\Utils\Strings;
use Delorius\View\Html;
use Shop\Catalog\Entity\Category;
use Shop\Commodity\Entity\Vendor;
use Shop\Commodity\Helpers\Options;
use Shop\Store\Entity\Currency;

class YmlGenerator extends Object
{
    public $select = array('goods_id', 'name', 'url', 'vendor_id', 'value', 'value_old', 'code', 'cid', 'model', 'brief', 'amount', 'article');

    protected $cats = array();
    protected $catIds = array();
    protected $catType;

    public function __construct($catType = Category::TYPE_GOODS)
    {
        $this->catType = $catType;
    }


    public function create($goods, $config = array())
    {

        $xml = '<?xml version="1.0" encoding="UTF-8"?>
               <!DOCTYPE yml_catalog SYSTEM "shops.dtd">
               <yml_catalog date="' . date('Y-m-d H:i') . '">
               <shop>' . "\n";

        $url_site = $config['site'];
        $param_show = $config['params'];
        $amount = $config['amount'];

        $xml .= '<name>' . $config['name'] . '</name>
                <company>' . $config['company'] . '</company>
                <url>' . $config['site'] . '</url>
                <platform>Delorius Framework</platform>
                <version>2.0</version>
                <agency>Седьмая группа</agency>
                <email>info@7thgroup.ru</email>' . "\n";

        #currencies
        $currencies = Currency::model()->find_all();
        if (count($currencies)) {
            $xml .= '<currencies>' . "\n";

            foreach ($currencies as $currency) {
                $xml .= _sf('<currency id="{0}" rate="{1}" plus="{2}" />',
                        $currency->code,
                        $currency->value == 1 ? 1 : 'CBRF',
                        0
                    ) . "\n";
            }
            $xml .= '</currencies>' . "\n";
        }
        #end currencies


        #categories
        $this->initCats();
        if (count($this->cats)) {
            $xml .= '<categories>' . "\n";
            $xml .= $this->getCategoriesXML();
            $xml .= '</categories>' . "\n";
        }
        #end categories


        $products = $ids = $idsVendor = array();
        foreach ($goods as $item) {
            $products[] = $item;
            $ids[] = is_array($item) ? $item['goods_id'] : $item->pk();
            $idsVendor[] = is_array($item) ? $item['vendor_id'] : $item->vendor_id;
        }
        unset($goods);
        Options::acceptFirstVariantsByProducts($products, $ids, true);

        if (count($idsVendor)) {
            $vendors = Vendor::model()->where('vendor_id', 'IN', $idsVendor)->find_all();
            $vendors = Arrays::resultAsArrayKey($vendors, 'vendor_id');
        }

        if ($config['adult'])
            $xml .= '<adult>true</adult>' . "\n";

        $xml .= '<offers>' . "\n";
        foreach ($products as $item) {

            if($item->value<=0){
                continue;
            }

            if (!$item->vendor_id) {
                $item->model = false;
            }

            $utm = '';
            if ($config['utm']) {
                $parser = new ParserString(array('id' => $item->pk()));
                $utm = $parser->render($config['utm']);
            }

            $xml .= _sf('<offer id="{0}" available="{1}" {2}>', $item->pk(), ($amount ? 'true' : ($item->amount == 0 ? 'false' : 'true')), $item->model ? 'type="vendor.model"' : '') . "\n";
            $xml .= _sf('<url>{0}</url>', Strings::escape($item->link() . $utm)) . "\n";
            $xml .= _sf('<price>{0}</price>', $item->getPrice(false, false)) . "\n";
            if ($item->value_old > 0)
                $xml .= _sf('<oldprice>{0}</oldprice>', $item->getPriceOld(false, false)) . "\n";
            $xml .= _sf('<currencyId>{0}</currencyId>', Environment::getContext()->getService('currency')->getCode()) . "\n";
            $xml .= _sf('<categoryId>{0}</categoryId>', $item->cid) . "\n";

            if ($item->image) {
                $xml .= _sf('<picture>{0}{1}</picture>', $url_site, $item->image->normal) . "\n";
            }

            $xml .= '<store>' . ($config['store'] ? 'true' : 'false') . '</store>' . "\n";
            $xml .= '<pickup>' . ($config['pickup'] ? 'true' : 'false') . '</pickup>' . "\n";
            $xml .= '<delivery>' . ($config['delivery'] ? 'true' : 'false') . '</delivery>' . "\n";

            if (!$item->model) {
                $xml .= _sf('<name>{0}</name>', Strings::escape($item->name)) . "\n";
            }

            if ($item->model) {
                $xml .= _sf('<typePrefix>{0}</typePrefix>', Strings::escape($this->catIds[$item->cid]['name'])) . "\n";
            }

            if ($item->vendor_id && isset($vendors[$item->vendor_id])) {
                $xml .= _sf('<vendor>{0}</vendor>', Strings::escape($vendors[$item->vendor_id]->name)) . "\n";
            }

            if ($item->model) {
                $xml .= _sf('<model>{0}</model>', Strings::escape($item->model)) . "\n";
            }

            if ($item->brief) {
                $xml .= _sf('<description>{0}</description>', Strings::escape($item->brief)) . "\n";
            } else {
                $sec = $item->getSections(true, true);
                if ($sec->loaded()) {
                    $xml .= _sf('<description>{0}</description>',
                            Strings::truncate(
                                Strings::escape(
                                    Html::clearTags(
                                        Environment::getContext()->getService('parser')->html($sec->text)
                                    )
                                ), 2997
                            )) . "\n";
                }
            }

            if ($config['sales_notes']) {
                // Элемент используется для отражения информации о минимальной сумме заказа,
                // минимальной партии товара или необходимости предоплаты,
                // а так же для описания акций, скидок и распродаж.
                // Допустимая длина текста в элементе — 50 символов.
                // sales_notes
                $xml .= _sf('<sales_notes>{0}</sales_notes>', $config['sales_notes']) . "\n";
            }


            if ($param_show) {
                $Characteristics = $item->getGroupCharacteristics();
                foreach ($Characteristics as $characs) {
                    if (count($characs['values'])) {
                        $items = $characs['values'];
                    } else {
                        $items = $characs;
                    }

                    foreach ($items as $item) {
                        $xml .= _sf('<param name="{0}" {2} >{1}</param>',
                                Strings::escape($item['chara']['name']),
                                Strings::escape($item['value']['name']),
                                $item['value']['unit'] ? 'unit="' . trim($item['value']['unit'], '.') . '"' : ''
                            ) . "\n";
                    }

                }
            }

            $xml .= '</offer>' . "\n";
        }
        $xml .= '</offers>' . "\n";

        $xml .= '</shop>
        </yml_catalog>';

        $file = $config['file'] ? $config['file'] : 'all';
        $path = _sf('{0}/{1}.xml', Environment::getContext()->getParameters('path.market'), $file);
        FileSystem::write($path, $xml);
    }

    protected function initCats()
    {
        if (!count($this->cats)) {
            $categories = Category::model()
                ->select(array('cid', 'id'), 'pid', 'name')
                ->type($this->catType)
                ->sort()
                ->active()
                ->find_all();
            foreach ($categories as $category) {
                $this->cats[$category['pid']][] = $category;
                $this->catIds[$category['id']] = $category;
            }
        }
    }

    public function getCategoriesXML($pid = 0)
    {
        $xml = '';
        if (isset($this->cats[$pid])) {
            foreach ($this->cats[$pid] as $cat) {
                $xml .= _sf('<category id="{0}" {1} >{2}</category>',
                        $cat['id'],
                        $cat['pid'] ? 'parentId="' . $cat['pid'] . '"' : '',
                        $cat['name']
                    ) . "\n";

                $xml .= $this->getCategoriesXML($cat['id']);
            }
        }

        return $xml;
    }
} 