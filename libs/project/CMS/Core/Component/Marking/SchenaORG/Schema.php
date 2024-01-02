<?php
namespace CMS\Core\Component\Marking\SchenaORG;

class Schema
{

    /**
     * @var array array(\CMS\Core\Component\Marking\SchenaORG\SchemaControl\BaseControl)
     */
    protected $scope = array();

    /**
     * @param string $name
     * @return \CMS\Core\Component\Marking\SchenaORG\SchemaControl\BaseControl
     */
    public function scope($name)
    {
        if (!isset($this->scope[$name])) {
            $class = self::getClassScope($name);
            $this->scope[$name] = new $class;
        }
        return $this->scope[$name];
    }

    /**
     * @param $name
     * @return string
     */
    public static function getClassScope($name)
    {
        $class = _sf('CMS\Core\Component\Marking\SchenaORG\SchemaControl\{0}', $name);
        if (class_exists($class)) {
            return $class;
        } else {
            return 'CMS\Core\Component\Marking\SchenaORG\SchemaControl\Thing';
        }
    }

} 