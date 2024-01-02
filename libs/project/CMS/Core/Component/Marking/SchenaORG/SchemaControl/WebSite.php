<?php
namespace CMS\Core\Component\Marking\SchenaORG\SchemaControl;

class WebSite extends BaseControl
{
    const SEARCH = '_search_';

    protected $type = 'http://schema.org/WebSite';

    public function search($target){
        $SearchAction = $this->prop('potentialAction')->setTag('form',true)->scope('SearchAction');
        $SearchAction->prop('target',str_replace(self::SEARCH,'{search_term_string}',$target));
        $SearchAction->prop('query-input',null,array(
            'type'=>'text',
            'name'=>'search_term_string'
        ))->setTag('input');
        return $this;
    }

} 