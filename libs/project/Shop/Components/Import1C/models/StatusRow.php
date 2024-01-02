<?php namespace Shop\Components\Import1C\models;

use Shop\Components\Import1C\interfaces\IImport1C;

class StatusRow
{
    public $datetime = '';
    public $status = IImport1C::STATUS_ERROR;
    public $statusMessages = array();
}
