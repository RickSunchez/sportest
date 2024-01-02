<?php
namespace Delorius\Security;

use Delorius\Core\Object;

Class Acl extends Object
{
    protected $id = 0;
    protected $role = array();    
    protected $roleName = array();
    protected $result = false;

    public function __construct()
    {
        $this->init();
    }

    public function init()
    {
        
    }
    
    
    public function addRole($subrole,$role=null,$note = null)
    {
        $this->id++;
        $pid = $this->getPid($role);
        
        $this->role[$pid][$this->id] = array(
            'name'=>$subrole,
            'id'=>$this->id,
            'pid'=>$pid,
            'note'=>$note
        );
                
        $this->roleName[$subrole] = array(
            'id'=>$this->id,
            'pid'=>$pid,
            'note'=>$note
        );
        
        return $this;
    }
    
    protected function getPid($role=null)
    {
       if(null!=$role)
           return $this->roleName[$role]['id'];
       else
           return 0;           
    }
    
    public function getRole()
    {
        return $this->role;
    }
    
    public function checkRole($name_role,array $role)
    {   
        if(!is_array($role))
            $role = array('0'=>$role);
        
        foreach($role as $k=>$role_parant)
        {            
            if($name_role==$role_parant)
                return true;
            
            $child = $this->role[$this->roleName[$role_parant]['id']];            
                        
            if(is_array($child))               
                $this->treeRole($name_role,$child);
        }
        
        $res = $this->result;
        $this->result = false;
        
        return $res;
            
    }
    
    protected function treeRole($name_role,array $child)
    { 
        foreach($child as $id => $role_child)
        {
            if ($name_role == $role_child['name'])             
                    return $this->result = true; 
            
            $child = $this->role[$this->roleName[$role_child['name']]['id']];            
            
            if(is_array($child)) 
                $this->treeRole($name_role,$child);
        }

    }


   

    
    
}
