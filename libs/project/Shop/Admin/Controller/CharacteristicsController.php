<?php

namespace Shop\Admin\Controller;

use CMS\Core\Helper\Helpers;
use Delorius\Application\UI\Controller;
use Delorius\Exception\Error;
use Delorius\Exception\OrmValidationError;
use Delorius\Utils\Arrays;
use Delorius\Utils\CSV\CSVWrite;
use Shop\Commodity\Entity\Characteristics;
use Shop\Commodity\Entity\CharacteristicsGoods;
use Shop\Commodity\Entity\CharacteristicsGroup;
use Shop\Commodity\Entity\CharacteristicsValues;
use Shop\Commodity\Entity\Goods;
use Shop\Commodity\Entity\Unit;

/**
 * @Template(name=admin)
 * @Admin
 */
class CharacteristicsController extends Controller
{

    /**
     * @SetTitle Характеристики #admin_chara?action=list
     * @AddTitle Список
     */
    public function listAction()
    {
        $chara = Characteristics::model()->cached()->sort()->find_all();
        $var['chara'] = Arrays::resultAsArray($chara);
        $groups = CharacteristicsGroup::model()->cached()->sort()->find_all();
        $var['groups'] = Arrays::resultAsArray($groups);
        $this->response($this->view->load('shop/goods/chara/list', $var));
    }

    /**
     * @SetTitle Характеристики #admin_chara?action=list
     * @AddTitle Группы
     */
    public function listGroupAction()
    {
        $groups = CharacteristicsGroup::model()->cached()->sort()->find_all();
        $var['groups'] = Arrays::resultAsArray($groups);
        $this->response($this->view->load('shop/goods/chara/list_group', $var));
    }

    /**
     * @SetTitle Характеристики #admin_chara?action=list
     * @AddTitle Добавить характеристику
     */
    public function addAction()
    {
        $units = Unit::model()->select()->cached()->sort()->find_all();
        $var['units'] = Arrays::resultAsArray($units, false);
        $var['filters'] = Arrays::dataKeyValue(Characteristics::getFilters());
        $this->response($this->view->load('shop/goods/chara/edit', $var));
    }

    /**
     * @SetTitle Характеристики #admin_chara?action=list
     * @AddTitle Редактировать характеристику
     * @Model(name=Shop\Commodity\Entity\Characteristics)
     */
    public function editAction(Characteristics $model)
    {
        $var['chara'] = $model->as_array();
        $var['values'] = Arrays::resultAsArray($model->getValues());
        $units = Unit::model()->select()->cached()->sort()->find_all();
        $var['units'] = Arrays::resultAsArray($units, false);
        $var['filters'] = Arrays::dataKeyValue(Characteristics::getFilters());
        $this->response($this->view->load('shop/goods/chara/edit', $var));
    }

    /**
     * @Post
     */
    public function addValueDataAction()
    {
        $post = $this->httpRequest->getPost();
        try {
            $value = new CharacteristicsValues();
            $value->values($post);
            $value->save(true);

            $arr = $value->as_array();
            if ($value->unit_id) {
                $unit = Unit::model($value->unit_id);
                $arr['unit'] = $unit->abbr;
            }
            $result['value'] = $arr;
            $result['ok'] = _t('CMS:Admin', 'These modified');

        } catch (OrmValidationError $e) {
            $result['errors'] = $e->getErrorsMessage();
        }
        $this->response($result);
    }


    /**
     * @Post
     */
    public function saveDataAction()
    {
        $post = $this->httpRequest->getPost();
        try {
            $chara = new Characteristics($post['chara'][Characteristics::model()->primary_key()]);
            $chara->values($post['chara']);
            $chara->save(true);

            foreach ($post['values'] as $value) {
                $chara->addValue($value);
            }

            $result['ok'] = _t('CMS:Admin', 'These modified');
            $result['chara'] = $chara->as_array();
            $result['values'] = Arrays::resultAsArray($chara->getValues());

        } catch (OrmValidationError $e) {
            $result['errors'] = $e->getErrorsMessage();
        }
        $this->response($result);
    }


    /**
     * @Post
     */
    public function deleteValueDataAction()
    {
        $id = $this->httpRequest->getPost('value_id');
        $value = new CharacteristicsValues($id);
        if ($value->loaded()) {
            $value->delete(true);
        }
        $this->response(array('ok'));
    }

    /**
     * @Post
     */
    public function changePosDataAction()
    {
        $post = $this->httpRequest->getPost();
        $chara = new Characteristics($post['id']);
        if ($chara->loaded()) {
            try {
                if ($post['type'] == 'edit') {
                    $chara->pos = (int)$post['pos'];
                } else if ($post['type'] == 'up') {
                    $chara->pos++;
                } else if ($post['type'] == 'down') {
                    $chara->pos--;
                }
                $chara->save(true);
                $result['ok'] = _t('CMS:Admin', 'Ready');
                $arrChara = Characteristics::model()->sort()->find_all();
                $result['chara'] = Arrays::resultAsArray($arrChara);

            } catch (OrmValidationError $e) {
                $result['errors'] = $e->getErrorsMessage();
            }
        }
        $this->response($result);
    }

    /**
     * @Post
     */
    public function deleteDataAction()
    {
        $id = $this->httpRequest->getPost('id');
        $chara = new Characteristics($id);
        if ($chara->loaded()) {
            $chara->delete(true);
        }
        $this->response(array('ok'));
    }

    /**
     * @Post
     */
    public function saveGroupDataAction()
    {
        $post = $this->httpRequest->getPost();
        try {
            $group = new CharacteristicsGroup($post['group_id']);
            $group->values($post);
            $group->save(true);

            $result['ok'] = _t('CMS:Admin', 'These modified');
        } catch (OrmValidationError $e) {
            $result['errors'] = $e->getErrorsMessage();
        }
        $groups = CharacteristicsGroup::model()->cached()->sort()->find_all();
        $result['groups'] = Arrays::resultAsArray($groups);
        $this->response($result);
    }

    /**
     * @Post
     */
    public function deleteGroupDataAction()
    {
        $post = $this->httpRequest->getPost();
        $group = new CharacteristicsGroup($post['group_id']);
        if ($group->loaded()) {
            $group->delete(true);
        }
        $this->response(array('ok'));
    }


    public function loadCharaAction()
    {

        if (!extension_loaded('zip')) {
            throw new Error('You dont have ZIP extension');
        }

        $orm = Characteristics::model()->select()->sort()->find_all();

        $csv = new CSVWrite();
        $csv->fields = array('chId', 'name', 'ownerId');
        foreach ($orm as $item) {
            $csv->addRow(array(
                'chId' => $item['character_id'],
                'name' => $item['name']
            ));
        }
        $csv->output('characteristics.csv');
        die;
    }

    public function loadValueAction()
    {

        if (!extension_loaded('zip')) {
            throw new Error('You dont have ZIP extension');
        }

        $values = CharacteristicsValues::model()->select()->order_by('character_id')->find_all();

        $orm = Unit::model()->select()->find_all();
        $units = Arrays::resultAsArrayKey($orm, 'unit_id', true);

        $csv = new CSVWrite();
        $csv->fields = array('chId', 'valueId', 'name', 'abbr', 'ownerId');
        foreach ($values as $value) {
            $csv->addRow(array(
                'chId' => $value['character_id'],
                'valueId' => $value['value_id'],
                'name' => $value['name'],
                'abbr' => $value['unit_id'] ? $units[$value['unit_id']]['abbr'] : ''
            ));
        }

        $csv->output('values.csv');
        die;
    }

    /**
     * @param $value_id
     * @throws Error
     */
    public function loadGoodsAction($value_id)
    {

        $var['value'] = $value = CharacteristicsValues::model($value_id);
        $var['chara'] = $chara = Characteristics::model($value->character_id);

        $orm = CharacteristicsGoods::model()
            ->select('target_id')
            ->where('value_id', '=', $value->pk())
            ->where('target_type', '=', Helpers::getTableId(Goods::model()))
            ->find_all();

        $ids = array();
        foreach ($orm as $item) {
            $ids[] = $item['target_id'];
        }

        $var['goods'] = array();
        if (count($ids)) {
            $var['goods'] = $goods = Goods::model()
                ->where('goods_id', 'in', $ids)
                ->find_all();
        }

        $this->response($this->view->load('shop/goods/chara/__list_goods', $var));

    }

}