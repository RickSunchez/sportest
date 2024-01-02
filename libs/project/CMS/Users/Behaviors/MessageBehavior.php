<?php
namespace CMS\Users\Behaviors;

use CMS\Users\Entity\Message;
use Delorius\Behaviors\ORMBehavior;
use Delorius\Exception\OrmValidationError;

class MessageBehavior extends ORMBehavior
{
    /**
     * @param string $text
     * @param int $owner_id
     */
    public function addMessage($text,$owner_id){
        try{
            $message = new Message();
            $message->text = $text;
            $message->owner_id = $owner_id;
            $message->owner_status = Message::STATUS_READ;
            $message->to_id = $this->getOwner()->pk();
            $message->to_status = Message::STATUS_NEW;
            $message->save(true);
            return $message;
        }catch (OrmValidationError $e){
            return false;
        }
    }

}