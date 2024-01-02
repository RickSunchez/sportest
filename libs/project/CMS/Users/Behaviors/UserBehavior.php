<?php
namespace CMS\Users\Behaviors;

use Delorius\Behaviors\ORMBehavior;
use Delorius\Core\ORM;

class UserBehavior extends ORMBehavior {

    /**
     * @var \Delorius\Security\User
     * @service user
     * @inject
     */
    public $user;

    public function currentUser(){
        $this->getOwner()->where('user_id','=',(int)$this->user->getId());
        return $this->getOwner();
    }

    /**
     * @return bool
     */
    public function isCurrentUser(){
        return $this->getOwner()->user_id == $this->user->getId()? true:false;
    }

    /**
     * @return int
     */
    public function userId(){
        return $this->getOwner()->user_id;
    }

    public function beforeSave(ORM $orm)
    {
        if(!$orm->loaded() && $orm->user_id == null)
            $orm->user_id = $this->user->getId();
    }




}