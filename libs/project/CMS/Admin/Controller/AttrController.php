<?php
namespace CMS\Admin\Controller;

use CMS\Users\Entity\AttrName;
use CMS\Users\Entity\GroupAttr;
use Delorius\Application\UI\Controller;
use Delorius\Exception\OrmValidationError;
use Delorius\Utils\Arrays;

/**
 * @Template (name=admin)
 * @Admin
 * @SetTitle  Атрибуты пользователя #admin_attr?action=list
 */
class AttrController extends Controller{

    /**
     * @AddTitle Список
     */
    public function listAction(){
        $var['groups'] = Arrays::resultAsArray(GroupAttr::model()->order_by('pos')->find_all());
        $this->response($this->view->load('cms/user/group_list', $var));
    }

    /**
     * @AddTitle Редактировать
     * @Model(name=\CMS\Users\Entity\GroupAttr,loaded=false)
     */
    public function editAction(GroupAttr $model){
        if($model->loaded()){
            $var['group'] = $model->as_array();
            $var['attributes_array'] = Arrays::resultAsArray($model->getAttributes());
        }
        $this->response($this->view->load('cms/user/attr', $var));
    }

    /**
     * @Post
     */
    public function saveDataAction(){
        $post = $this->httpRequest->getPost();
        try{
            $group = new GroupAttr($post['group']['group_id']);
            $group->values($post['group'])->save(true);
            foreach($post['attr'] as $key=>$v){
                if(!empty($v['name']) && $group->addAttribute($v)!==TRUE ){
                    $var['errors'][] = _t('CMS:Admin','Code not empty {0}',$v['name']);
                }
            }
            $var['attr'] = Arrays::resultAsArray($group->getAttributes());
            $var['group'] = $group->as_array();
            $var['ok'] = _t('CMS:Admin','These modified');
        }catch (OrmValidationError $e){
            $var['errors'] = $e->getMessage();
        }
        $this->response($var);
    }

    /**
     * @Post
     * @Model(name=\CMS\Users\Entity\GroupAttr)
     */
    public function deleteDataAction(GroupAttr $model){
        $model->delete(true);
        $this->response(array('ok'));
    }


    /**
     * @Post
     * @Model(name=\CMS\Users\Entity\AttrName)
     */
    public function deleteAttrDataAction(AttrName $model){
        $model->delete(true);
        $this->response(array('ok'));
    }
}