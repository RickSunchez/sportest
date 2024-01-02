<?php
namespace CMS\Core\Component\Marking\SchenaORG\SchemaControl;


class WebPage extends BaseControl
{

    protected $type = 'http://schema.org/WebPage';
    protected $links = array();

    public function addLink($name, $link)
    {
        $this->isChange = true;
        $this->links[] = array('name' => $name, 'url' => $link);
    }

    protected function innerRender()
    {
        $this->breadcrumbRender();
        $html = '';
        $html .= parent::innerRender();
        return $html;
    }

    /**
     * @return string
     */
    protected function breadcrumbRender()
    {
        if (count($this->links)) {
            $BreadcrumbList = $this->prop('breadcrumb')->scope('BreadcrumbList');
            foreach ($this->links as $key => $link) {
                $ListItem = $BreadcrumbList->prop('itemListElement')->scope('ListItem');
                $ListItem->prop('item', $link['name'], array('href' => $link['url']))->setTag('a', true);
                $ListItem->prop('name', $link['name'])->setTag('span', true);
                $ListItem->prop('position', $key + 1);
            }
        }
    }


}