<?php
namespace CMS\Cabinet\Controller;

/**
 * @User
 */
class HomeController extends BaseController{

    public function indexAction(){
        $this->response('<h1>WELCOME</h1>');
    }
}