<?php
namespace CMS\Mail\Controller;

use CMS\Core\Component\Register;
use CMS\Mail\Entity\Subscription;
use CMS\Mail\Model\SubscriberBuilder;
use Delorius\Application\UI\Controller;
use Delorius\Exception\Error;

class SubscriberController extends Controller
{

    public function unsubAction($group_id, $hash)
    {
        try {




            $subscription = new Subscription((int)$group_id);
            $builder = new SubscriberBuilder($hash);
            $sub = $builder->getOwner();
            if ($subscription->loaded() && $sub->loaded()) {
                $subscription->unsubscribe($sub);
            }elseif($sub->loaded()){
                $sub->status = 0;
                $sub->save();

            }
            if($sub->loaded()){
                $register = $this->container->getService('register');
                $register->add(
                    Register::TYPE_ATTENTION,
                    Register::SPACE_SITE,
                    'Email отписался: [email]',
                    $sub
                );
            }
        } catch (Error $e) {
            $this->container->getService('logger')->error($e->getMessage(), 'SubscriberBuilder');
        }


        echo '<br /><br /><br /><br /><br /><h1 style="text-align: center">'._t('CMS:Mail','You unsubscribed from this mailing').'</h1>';
        die;


    }


}