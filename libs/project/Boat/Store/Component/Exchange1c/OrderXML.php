<?php
namespace Boat\Store\Component\Exchange1c;

use Delorius\Utils\Arrays;
use Shop\Store\Entity\Order;

class OrderXML
{
    /** @var \DOMDocument */
    protected $root = null;
    /** @var Order */
    protected $orders = null;

    public function __construct()
    {
        $this->root = new \DOMDocument('1.0');
        $this->root->encoding = 'UTF-8';
        $this->root->formatOutput = true;
        $this->root->preserveWhiteSpace = false;
    }

    public function create($orders, $collections)
    {
        $collections = is_array($collections)
            ? $collections
            : [];

        $commercial = $this->appendChildNode("КоммерческаяИнформация");
        $commercial->appendChild($this->appendChildAttr('ВерсияСхемы', '2.05'));
        $this->root->appendChild($commercial);

        if (count($orders))
            foreach ($orders as $order) {
                $doc = $this->appendChildNode('Документ');
                $commercial->appendChild($doc);

                $doc->appendChild($this->appendChildNode('Ид', $order->pk()));
                $doc->appendChild($this->appendChildNode('Номер', $order->getNumber()));
                $doc->appendChild($this->appendChildNode('Дата', date('Y-m-d', $order->date_cr)));
                $doc->appendChild($this->appendChildNode('ХозОперация', 'Заказ товара'));
                $doc->appendChild($this->appendChildNode('Роль', 'Продавец'));
                $doc->appendChild($this->appendChildNode('Валюта', 'RUB'));
                $doc->appendChild($this->appendChildNode('Курс', '1'));
                $doc->appendChild($this->appendChildNode('Сумма', $order->getPrice(null, false)));
                $customers = $this->appendChildNode('Контрагенты');
                $doc->appendChild($customers);
                $customer = $this->appendChildNode('Контрагент');
                $customers->appendChild($customer);
                $phone = $order->getOptions('phone');
                $customer->appendChild($this->appendChildNode('Ид', $this->phoneToId($phone['value'])));
                $doc->appendChild($this->appendChildNode('Время', date('H:m:i', $order->date_cr)));

                if ($order->email) {
                    $contacts = $this->appendChildNode('Контакты');
                    $customer->appendChild($contacts);
                    $contact = $this->appendChildNode('Контакт');
                    $contacts->appendChild($contact);
                    $contact->appendChild($this->appendChildNode('Тип', 'Почта'));
                    $contact->appendChild($this->appendChildNode('Значение', $order->email));
                }

                $opts = $order->getOptions();
                $comment = '';
                foreach ($opts as $opt) {
                    $comment .= _sf("{0}:{1}\n\n", $opt['name'], $opt['value']);
                }
                $doc->appendChild($this->appendChildNode('Комментарий', $comment));
                $goods = $this->appendChildNode('Товары');
                $doc->appendChild($goods);

                foreach ($order->getItems() as $item) {
                    $product = $this->appendChildNode('Товар');
                    $goods->appendChild($product);

                    $product_data = Arrays::get($item->getConfig(), 'goods');

                    $externalId = key_exists('external_id', $product_data)
                        ? $product_data['external_id']
                        : null;
                    if (!$externalId) {
                        continue;
                    }

                    $parentEId = key_exists('parent_external_id', $product_data)
                        ? (
                            empty($product_data['parent_external_id'])
                                ? null
                                : $product_data['parent_external_id']
                        )
                        : null;
                    $collectionEId = key_exists($externalId, $collections)
                        ? $collections[$externalId]
                        : null;

                    $parentEId = (is_null($parentEId))
                        ? (
                            is_null($collectionEId)
                                ? ''
                                : $collectionEId . '#'
                        )
                        : $parentEId . '#';

                    $product->appendChild(
                        $this->appendChildNode(
                            'Ид',
                            _sf('{0}{1}', $parentEId, $externalId)
                        )
                    );
                    $product->appendChild($this->appendChildNode('Наименование', $product_data['name']));

                    $be = $this->appendChildNode('БазоваяЕдиница', 'шт.');
                    $be->appendChild($this->appendChildAttr('МеждународноеСокращение', 'PCE'));
                    $be->appendChild($this->appendChildAttr('НаименованиеПолное', 'Штука'));
                    $product->appendChild($be);

                    $product->appendChild($this->appendChildNode('ЦенаЗаЕдиницу', $item->value));
                    $product->appendChild($this->appendChildNode('Количество', $item->amount));
                    $product->appendChild($this->appendChildNode('Сумма', $item->value * $item->amount));
                }

                if ($order->paid) {
                    $requisites = $this->appendChildNode('ЗначенияРеквизитов');
                    $doc->appendChild($requisites);
                    $requisites->appendChild($this->appendChildRequisite('Дата оплаты', date('Y-m-d H:i:s', $order->date_paid)));
                    $requisites->appendChild($this->appendChildRequisite('Метод оплаты', 'Экваринг Сбербанк'));
                    $requisites->appendChild($this->appendChildRequisite('Заказ оплачен', 'true'));
                }
            }

        return $this;
    }

    /**
     * @param $name
     * @return int
     */
    public function save($name)
    {
        $bits = $this->root->save($name);
        return $bits;
    }

    /**
     * @return string
     */
    public function saveXML()
    {
        return $this->root->saveXML();
    }


    protected function phoneToId($phone)
    {
        $phone = str_replace('+7', '8', $phone);
        $phone = str_replace(array(' ', '-', '(', ')'), '', $phone);
        return $phone;
    }

    /**
     * @param $name
     * @param null $value
     * @return \DOMElement
     */
    protected function appendChildNode($name, $value = null)
    {
        $el = $this->root->createElement($name);
        if ($value) {
            $el_text = $this->root->createTextNode($value);
            $el->appendChild($el_text);
        }
        return $el;
    }

    /**
     * @param $name
     * @param $value
     * @return \DOMAttr
     */
    protected function appendChildAttr($name, $value)
    {
        $attr = $this->root->createAttribute($name);
        $attr_value = $this->root->createTextNode($value);
        $attr->appendChild($attr_value);
        return $attr;
    }

    /**
     * @param $name
     * @param $value
     * @return \DOMElement
     */
    protected function appendChildRequisite($name, $value)
    {
        $req = $this->root->createElement('ЗначениеРеквизита');
        $req->appendChild($this->appendChildNode('Наименование', $name));
        $req->appendChild($this->appendChildNode('Значение', $value));
        return $req;
    }

}