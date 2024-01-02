<?php
namespace Shop\Payment\Behaviors;

use Delorius\Behaviors\ORMBehavior;
use Delorius\Core\ORM;
use Shop\Payment\Entity\Account;

class EditAccountBehavior extends ORMBehavior
{
    /**
     * @var \Delorius\Tools\ILogger
     * @service logger
     * @inject
     */
    public $_logger;

    /**
     * @var \Delorius\Security\User
     * @service user
     * @inject
     */
    public $_user;
    /**
     * @var \Delorius\Http\IRequest
     * @service httpRequest
     * @inject
     */
    public $_httpRequest;


    /** @return bool */
    public function paid(){
        $clsCallback = $this->getOwner()->callback;
        $callback = new $clsCallback($this->getOwner());
        return $callback->paid();
    }

    /** @return string */
    public function success(){
        $clsCallback = $this->getOwner()->callback;
        $callback = new $clsCallback($this->getOwner());
        return $callback->success();
    }

    /** @return string */
    public function fail(){
        $clsCallback = $this->getOwner()->callback;
        $callback = new $clsCallback($this->getOwner());
        return $callback->fail();
    }

    public function onBeforeDelete(ORM $orm){
        $ip = $this->_httpRequest->getRemoteAddress();
        $id = $this->_user->getId();
        $this->_logger->info(_sf('Try to remove account id={0},user_id={1},ip={2}',$orm->pk(),$id,$ip),'Account');
        $orm->status = Account::STATUS_DELETE;
        $orm->desc = $orm->desc._sf(' УДАЛЕН {0}',date('d-m-y H:i:s'));
        $orm->save();
        $orm->clear();
    }

    public function beforeSave(ORM $orm)
    {
        if(!$orm->loaded() && !$orm->ip)
            $orm->ip = $this->_httpRequest->getRemoteAddress();
    }


}