<?php
namespace CMS\Core\Component\Marking\SchenaORG\SchemaControl;

use CMS\Core\Component\Marking\SchenaORG\IRender;
use CMS\Core\Component\Marking\SchenaORG\Prop;

class BaseControl implements IRender
{

    protected $type;
    protected $items = array();
    protected $prop = null;
    protected $isChange = false;

    public function __construct(Prop $prop = null)
    {
        $this->prop = $prop;
    }

    public function prop($name, $content = null, $attr = array())
    {
        $this->isChange = true;
        $prop = new Prop($name, $content, $attr);
        $this->items[] = $prop;
        return $prop;
    }

    /**
     * @return string
     */
    protected function innerRender()
    {
        $html = '';
        foreach ($this->items as $prop) {
            $html .= $prop->render();
        }
        return $html;
    }

    public function render($class = null)
    {
        if (!$this->isChange) {
            return '';
        }
        $itemprop = '';
        if ($this->prop != null) {
            $itemprop = _sf('{1} itemprop="{0}" ', $this->prop->name(), $this->prop->isEnd() ? $this->prop->tag() : 'div');
        } else {
            $itemprop = 'div';
        }
        $div = _sf('<{0} itemscope itemtype="{1}" {2} >',
            $itemprop,
            $this->type,
            $class != null ? 'class="' . $class . '" ' : '');
        $div .= $this->innerRender();

        if ($this->prop != null) {
            $div .= _sf('</{0}>', $this->prop->isEnd() ? $this->prop->tag() : 'div');
        } else {
            $div .='</div>';
        }
        return $div;
    }

} 