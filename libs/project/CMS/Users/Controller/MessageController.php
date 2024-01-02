<?php
namespace CMS\Users\Controller;

use CMS\Core\Entity\Image;
use CMS\Users\Entity\AttrName;
use CMS\Users\Entity\Message;
use CMS\Users\Entity\User;
use CMS\Users\Entity\UserAttr;
use Delorius\Application\UI\Controller;
use Delorius\Core\Common;
use Delorius\Core\DateTime;
use Delorius\DataBase\DB;

/**
 * @User
 */
class MessageController extends Controller{

    protected $config = array();

    public function before()
    {
        $this->config = Common::getConfig('CMS:Users');
        if ($this->config['template']['message'])
            $this->template($this->config['template']['message']);
        if ($this->config['layout']['message'])
            $this->layout($this->config['layout']['message']);
    }

    public function listAction(){

    }

    /**
     * @param $user_id
     * @Model(name=CMS\Users\Entity\User,field=user_id)
     */
    public function toAction(User $model){
        $messages = Message::model()
            ->whereDialog($this->user->getId(),$model->pk())
            ->order_pk('desc')
            ->find_all();

        $var['messages'] = array();
        foreach($messages as $msg){
            $var['messages'][] = $msg;
        }

        $var['user'] = $model;
        $this->response($this->view->load('cabinet/im/dialog',$var));
    }

    /**
     * @Post
     */
    public function addMessageDataAction(){
        $post = $this->httpRequest->getPost();
        $user = new User($post['to_id']);
        if($user->loaded()){
            $message = $user->addMessage($post['text'],$this->user->getId());
        }
        $this->response(array('ok'));
    }


    /**
     * @Model(name=\CMS\Users\Entity\User,field=id)
     */
    public function privateMessageAction(User $model){
        $var['owner_user'] = $this->user->getIdentity()->getData();
        $var['to_user'] = $model;
        if( $model->loaded() ){
            $var['users'] = array(
                $model->pk() => $model->as_array(),
                $this->user->getId() => $var['owner_user']
            );
            if (sizeof($this->config['user_attrs'])) {
                $attrName = AttrName::model()->where('code', 'IN', $this->config['user_attrs'])->sort()->find_all();
                $var['user_attrs'] = $var['attr_name'] = $attrIds = array();
                foreach ($attrName as $attr) {
                    $var['attr_name'][] = $attr;
                    $attrIds[] = $attr->pk();
                }
                if (sizeof($attrIds)) {
                    $attrs = UserAttr::model()->where('user_id', '=', $model->pk())->where('attr_id', 'IN', $attrIds)->find_all();
                    $var['user_attrs'] = array();
                    foreach ($attrs as $attr) {
                        $var['user_attrs'][$attr->user_id][$attr->attr_id] = $attr->value;
                    }
                }
            }
            $var['avatar'] = Image::model()
                ->whereByTargetType($model)
                ->whereByTargetId($model->pk())
                ->find();
            $msg = new Message();
            DB::update($msg->table_name())
                ->set(array('to_status'=>$msg::STATUS_READ))
                ->where('to_status','=',$msg::STATUS_NEW)
                ->where('owner_id','=',$model->pk())
                ->where('to_id','=',$this->user->getId())
                ->execute();
            $messages = $msg
                ->whereDialog($this->user->getId(),$model->pk())
                ->find_all();
            $var['messages'] = array();
            foreach($messages as $msg){
                $msg->date_cr = DateTime::dateFormat($msg->date_cr, true);
                $var['messages'][] = $msg->as_array();
            }
            $this->response($this->view->partial('cabinet/im/dialog', $var));
        }
    }

    public function listDialogsAction(){
        $dialogs = DB::select('owner_id', 'to_id', DB::expr('MIN(`to_status`) AS `st`'))
            ->from(Message::model()->table_name())
            ->where_open()
                ->where('owner_id', '=', $this->user->getId())
                ->where('owner_status', '<>', Message::STATUS_DELETE)
            ->where_close()
            ->or_where_open()
                ->where('to_id', '=', $this->user->getId())
                ->where('to_status', '<>', Message::STATUS_DELETE)
            ->where_close()
            ->group_by('to_id','owner_id')
            ->order_by(Message::model()->primary_key())
            ->execute();
        $ids = $var['new'] = array();
        foreach($dialogs as $d){
            $ids[$d['to_id']] = $d['to_id'];
            $ids[$d['owner_id']] = $d['owner_id'];
            $var['new'][$d['owner_id']] = $d['st'];
        }
        unset($ids[$this->user->getId()]);
        if( $ids ){
            $var['users'] = User::model()->whereUserIds($ids)->find_all();
            if (sizeof($this->config['user_attrs'])) {
                $attrName = AttrName::model()->where('code', 'IN', $this->config['user_attrs'])->sort()->find_all();
                $var['user_attrs'] = $var['attr_name'] = $attrIds = array();
                foreach ($attrName as $attr) {
                    $var['attr_name'][] = $attr;
                    $attrIds[] = $attr->pk();
                }
                if (sizeof($attrIds)) {
                    $attrs = UserAttr::model()->where('user_id', 'in', $ids)->where('attr_id', 'IN', $attrIds)->find_all();
                    $var['user_attrs'] = array();
                    foreach ($attrs as $attr) {
                        $var['user_attrs'][$attr->user_id][$attr->attr_id] = $attr->value;
                    }
                }
            }
        }
        $this->response($this->view->partial('cabinet/im/list', $var));
    }

    public function clearDialogDataAction($id){
        $msg = new Message();
        DB::update($msg->table_name())
            ->where('owner_id','=',$this->user->getId())
            ->where('to_id','=',$id)
            ->set(array('owner_status'=>Message::STATUS_DELETE))
            ->execute();
        DB::update($msg->table_name())
            ->where('owner_id','=',$id)
            ->where('to_id','=',$this->user->getId())
            ->set(array('to_status'=>Message::STATUS_DELETE))
            ->execute();
        $this->response(array('ok'));
    }

}