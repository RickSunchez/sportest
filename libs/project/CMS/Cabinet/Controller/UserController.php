<?php
namespace CMS\Cabinet\Controller;

use CMS\Users\Entity\AttrName;
use CMS\Users\Entity\User;
use CMS\Users\Entity\UserAttr;
use Delorius\Exception\Error;
use Delorius\Exception\OrmValidationError;
use Delorius\Utils\Arrays;

/**
 * @User
 */
class UserController extends BaseController
{

    /**
     * @AddTitle Пользовательские данные
     */
    public function indexAction()
    {
        $user = new User($this->user->getId());
        $var['user'] = $user->as_array();
        $var['image'] = $user->getImage()->as_array();

        #attr start
        $var['attr_name'] = array();
        $var['user_attrs'] = array();
        if(sizeof($this->config['user']['form']['attrs'])){
            $attrName = AttrName::model()->where('code','IN',$this->config['user']['form']['attrs'])->sort()->find_all();
            $attrIds = array();
            foreach($attrName as $attr){
                $var['attr_name'][] = $attr->as_array();
                $attrIds[] = $attr->pk();
            }
            if(sizeof($attrIds)){
                $attrs = UserAttr::model()->where('user_id', '=', $user->pk())->where('attr_id','IN',$attrIds)->find_all();
                $var['user_attrs'] = Arrays::resultAsArrayKey($attrs,'attr_id',true);
            }
        }
        #attr end

        $this->response($this->view->load('cabinet/user/edit', $var));
    }


    /**
     * @Post
     */
    public function editDataAction()
    {
        $post = $this->httpRequest->getPost();
        $user = new User($this->user->getId());
        if ($user->loaded()) {
            try {
                $user->set('login',$post['user']['login']);

                if($post['user']['newPassword'] && $post['user']['newPassword'] == $post['user']['newPasswordVerify']){
                    if ($user->hashPassword($post['user']['password']) == $user->password) {
                        $user->password = $post['user']['newPassword'];
                    }else{
                        throw new Error(_t('CMS:Cabinet', 'Incorrect password'));
                    }
                }
                $user->save(true);
                foreach($post['attr'] as $attr){
                    $user->addAttr($attr);
                }

                $result['ok'] = _t('CMS:Cabinet', 'Your data is changed');
                $attrs = UserAttr::model()->where('user_id', '=', $user->pk())->find_all();
                $result['user_attrs'] = Arrays::resultAsArrayKey($attrs,'attr_id',true);

            } catch (OrmValidationError $e) {
                $result['errors'] = $e->getErrorsMessage();
            } catch (Error $e){
                $result['errors'][] = $e->getMessage();
            }
        } else {
            $result['errors'][] = _t('CMS:Cabinet', 'Incorrect password');
        }
        $this->response($result);
    }

    /**
     * @Post
     */
    public function uploadDataAction()
    {
        $result = array('error' => _t('CMS:Admin','Could not load file'));
        $object_json = $this->httpRequest->getPost('user');
        $object_arr = json_decode($object_json, true);
        $object = new User($object_arr['user_id']);
        if ($object->loaded()) {
            $file = $this->httpRequest->getFile('file');
            $res = $object->setImage($file);
            if ($res) {
                $result = $res;
            }
        }
        $this->response($result);
    }


}