<?php
namespace CMS\Users\Behaviors;

use Delorius\Behaviors\ORMBehavior;
use Delorius\Core\ORM;

class EditUserBehavior extends ORMBehavior {

    /**
     * @var \Delorius\Http\IRequest
     * @service httpRequest
     * @inject
     */
    public $httpRequest;


    public function beforeSave(ORM $orm)
    {
        $orm->ip = $this->httpRequest->getRemoteAddress();
    }




}