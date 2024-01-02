<?php namespace Boat\Store\Cron\Export1C\components\goods\helpers;

class ImportFileHelper
{
    const DELETE_ACTION = '77a5adc5-b75c-11e7-8404-001e67647f08'; # Удалить с сайта
    const IS_DELETED = '2a842d78-e0c1-11e3-b038-001e67647f08'; # Удален

    protected $categories;
    protected $properties;

    public function __construct(
        $categories,
        $properties
    ) {
        $this->categories = $categories;
        $this->properties = $properties;
    }

    public function extractArticle($goods)
    {
        $article = '';
        if (count($goods->ЗначенияРеквизитов->ЗначениеРеквизита)) {
            foreach ($goods->ЗначенияРеквизитов->ЗначениеРеквизита as $props) {
                if ($props->Наименование->__toString() == 'Код') {
                    $article = $props->Значение->__toString();
                }
            }
        }

        if ($article) {
            return trim($article);
        }

        if ($goods->Код && !empty($goods->Код)) {
            return trim($goods->Код->__toString());
        }

        if ($goods->Артикул && !empty($goods->Артикул)) {
            return trim($goods->Артикул->__toString());
        }

        return trim($article);
    }

    public function extractAdditionalArticles($goods)
    {
        if (!is_array($this->properties['names'])) {
            return null;
        }

        $goodsPropsExists = (bool)count($goods->ЗначенияСвойств->ЗначенияСвойства);
        if (!$goodsPropsExists) {
            return null;
        }

        $goodsAArticles = array();
        foreach ($goods->ЗначенияСвойств->ЗначенияСвойства as $value) {
            $propId = $value->Ид->__toString();

            $propName = $this->properties['names'][$propId];
            $propValue = $value->Значение->__toString();

            if (empty($propValue)) {
                continue;
            }

            if (!mb_stristr($propName, 'артикул')) {
                continue;
            }

            $goodsAArticles[$propId] = array(
                'name' => $propName,
                'value' => $propValue
            );
        }

        return json_encode($goodsAArticles);
    }

    public function extractDeleteFlag($goods)
    {
        $goodsPropsExists = (bool)count($goods->ЗначенияСвойств->ЗначенияСвойства);
        if (!$goodsPropsExists) {
            return false;
        }
        
        foreach ($goods->ЗначенияСвойств->ЗначенияСвойства as $prop) {
            $code = $prop->Значение->__toString();

            if ($code == self::DELETE_ACTION) {
                return true;
            }
            if ($code == self::IS_DELETED) {
                return true;
            }
        }
    }
}
