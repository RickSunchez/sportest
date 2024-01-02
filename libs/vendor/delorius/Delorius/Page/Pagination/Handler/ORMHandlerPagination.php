<?php
namespace Delorius\Page\Pagination\Handler;

use Delorius\Core\ORM;
use Delorius\Page\Pagination\IHandlerPagination;

class ORMHandlerPagination implements IHandlerPagination {

    /** @var \Delorius\Core\ORM  */
    protected $orm;
    protected $limit,$offset = 0;

    public function __construct(ORM $orm){
        $this->orm =  $orm;
    }

    /** @return int */
    public function count(){
        $orm = clone $this->orm;
        return  $orm->count_all();
    }

    /** @return \Delorius\DataBase\Result */
    public function result(){

        if($this->limit){
            $this->orm->limit($this->limit);
            $this->orm->offset($this->offset);
        }

        return $this->orm->find_all();
    }

    public function limit($limit)
    {
        $this->limit = $limit;
    }

    public function offset($offset)
    {
        $this->offset = $offset;
    }
}