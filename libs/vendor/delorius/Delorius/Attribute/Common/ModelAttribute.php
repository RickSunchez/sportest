<?php

namespace Delorius\Attribute\Common;

use Delorius\Attribute\Attribute;
use Delorius\Attribute\IAttributeOnStartup;
use Delorius\Exception\Error;
use Delorius\Exception\ForbiddenAccess;
use Delorius\Exception\NotFound;

/**
 * Class ModelAttribute
 * @package Delorius\Attribute\Common
 * Example: @Model(field=id,name=\Delorius\Core\Orm,loaded=true)
 */
class ModelAttribute extends Attribute implements IAttributeOnStartup
{
    /**
     * Default = 'id'
     * @var  string
     */
    protected $field;

    /**
     * Name class orm
     * @var string
     */
    protected $name;
    /**
     * Default = true
     * Проверка на заргузку, если что NotFound
     * @var bool
     */
    protected $loaded;


    function onStartup(\Delorius\Application\UI\Controller $controller)
    {
        $routerId = $controller->getRouter($this->field);
        $id = ((int)$routerId == 0) ? $controller->httpRequest->getRequest($this->field) : (int)$routerId;

        $model = new $this->name($id);
        if ($this->loaded) {
            if (!$model->loaded()) {
                throw new NotFound('Not found model "' . $this->name . '" by ' . $this->field . '="' . $id . '"');
            }
        }
        $controller->setRouter('model', $model);
    }

    function setParams(array $params = null)
    {
        $this->field = isset($params['field']) ? $params['field'] : 'id';
        if (!isset($params['name']) || !class_exists($params['name'])) {
            throw new Error('Not found class orm "' . $params['name'] . '" ');
        }
        $this->name = $params['name'];
        $this->loaded = isset($params['loaded']) ? ($params['loaded'] ? true : false) : true;
    }
}
