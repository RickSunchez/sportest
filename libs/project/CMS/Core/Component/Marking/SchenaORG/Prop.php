<?php
namespace CMS\Core\Component\Marking\SchenaORG;


class Prop implements IRender
{
    protected $tagName = 'meta';
    protected $tagEnd = false;
    /** @var  \CMS\Core\Component\Marking\SchenaORG\SchemaControl\BaseControl */
    protected $scope = null;
    /** @var  string */
    protected $name;
    /** @var  string */
    protected $content;
    /** @var array */
    protected $attr = array();

    public function __construct($name, $content = null, $attr = array())
    {
        $this->name = $name;
        $this->content = $content;
        $this->attr = $attr;
    }

    /**
     * @param $tag
     * @param bool $end
     * @return $this
     */
    public function setTag($tag, $end = false)
    {
        $this->tagName = $tag;
        $this->tagEnd = $end;
        return $this;
    }

    /**
     * @param $name
     * @return \CMS\Core\Component\Marking\SchenaORG\SchemaControl\BaseControl
     */
    public function scope($name)
    {
        $class = Schema::getClassScope($name);
        $this->scope = new $class($this);
        return $this->scope;
    }

    /**
     * @return string
     */
    public function name()
    {
        return $this->name;
    }

    public function tag()
    {
        return $this->tagName;
    }

    public function isEnd()
    {
        return $this->tagEnd;
    }

    public function render()
    {
        if ($this->scope == null) {
            $block = _sf('<{0} itemprop="{1}" ', $this->tagName, $this->name);

            if (count($this->attr)) {
                foreach ($this->attr as $name => $value) {
                    $block .= _sf(' {0}={1} ', $name, $value);
                }
            }

            if ($this->tagEnd) {
                $block .= _sf('>{0}</{1}>', $this->content, $this->tagName);
            } else {
                $block .= _sf(' {0} />', $this->content ? 'content="' . $this->content . '"' : '');
            }

            return $block;
        } else {
            return $this->scope->render();
        }
    }


} 