<?php
namespace CMS\Go\Controller;

use CMS\Go\Entity\Go;
use CMS\Go\Entity\GoStat;
use CMS\Go\Model\GoCookieHelper;
use CMS\Mail\Model\SubscriberBuilder;
use Delorius\Application\UI\Controller;

class RedirectController extends Controller{

    public function homeAction(){
        $this->httpResponse->redirect(link_to('homepage'));
    }

    public function indexAction($url){
        $go = new Go();
        $go->where('hash','=',md5($url))->find();
        if($go->loaded()){
            $go->visit++;
            $go->save();
            $this->saveStatic($go->pk());
            GoCookieHelper::SetCookie($go->pk());
            $this->httpResponse->redirect($go->redirect);
        }else{
            $this->httpResponse->redirect(link_to('homepage'));
        }
    }

    public function emailAction($url,$hash){
        $go = new Go();
        $go->where('hash','=',md5($url))->find();
        if($go->loaded()){
            $go->visit++;
            $go->save();
            $this->saveStatic($go->pk(),true);
            $this->saveEmail($hash);
            GoCookieHelper::SetCookie($go->pk(),true);
            $this->httpResponse->redirect($go->redirect);
        }else{
            $this->httpResponse->redirect(link_to('homepage'));
        }
    }


    private function saveStatic($go_id,$isMail = false){

        $goStat = new GoStat();
        $goStat->go_id = $go_id;
        $goStat->ip = $this->httpRequest->getRemoteAddress();
        $goStat->url_ref = $this->httpRequest->getRemoteHost();
        if($isMail){
            $goStat->is_mail = 1;
        }
        $goStat->save();
    }

    private function saveEmail($hash){

        $sub = SubscriberBuilder::factory($hash)->getOwner();
        if($sub ->loaded()){
            $sub ->ip = $this->httpRequest->getRemoteAddress();
            $sub ->save();
        }
    }


}