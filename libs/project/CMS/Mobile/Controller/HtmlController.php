<?php
namespace CMS\Mobile\Controller;

use CMS\Go\Entity\Go;
use CMS\Go\Entity\GoStat;
use CMS\Go\Model\GoCookieHelper;
use CMS\Mail\Model\SubscriberBuilder;
use Delorius\Application\UI\Controller;
use Delorius\Core\Common;

class HtmlController extends Controller
{

    public function clickPartial($tel = null)
    {
        $config = Common::getConfig('CMS:Mobile');
        $var['tel'] = $tel ? $tel : $config['phone'];
        $work = true;
        $week = date('N');
        if (!$config['week'][$week]) {
            $work = false;
        }
        $time = false;
        $hour = date('G');
        if (
            $config['hour']['with'] <= $hour &&
            $config['hour']['on'] >= $hour
        ) {
            $time = true;
        }
        if (!$time) {
            $work = false;
        }
        $var['work'] = false;
        $browser = new \Browser();
        if ($browser->isMobile()) {
            if (!$work) {
                return null;
            }
            $this->response($this->view->load('cms/mobile/click', $var));
        } else {
            $this->response($this->view->load('cms/mobile/phone', $var));
        }
    }


}