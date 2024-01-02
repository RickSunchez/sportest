<?php
namespace CMS\Users\Controller;

use Delorius\Application\UI\Controller;

/**
 * @User(isLoggedIn=false)
 */
class HtmlController extends Controller{

    public function authPartial(){
        if($this->user->isLoggedIn()){
            $var['user'] = $this->user->getIdentity();
            $this->response($this->view->load('cms/authorized/_is_login',$var));
        }else{
            $this->response($this->view->load('cms/authorized/_is_not_login'));
        }
    }

}