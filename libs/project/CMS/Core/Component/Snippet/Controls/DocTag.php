<?php

namespace CMS\Core\Component\Snippet\Controls;

use CMS\Core\Component\Snippet\AParserRenderer;
use CMS\Core\Entity\Document;
use Delorius\View\Html;

class DocTag extends AParserRenderer
{

    public function render()
    {
        if ($this->path == 'category') {
            $id = $this->query['id'];
            $list = Document::model()
                ->where('cid', '=', $id)
                ->select('file_id', 'ext', 'size', 'path', 'title')
                ->cached()
                ->find_all();

            if (count($list)) {

                $container = Html::el('div', array('class' => 'b-documents'));

                foreach ($list as $item) {
                    $doc = Document::mock($item);
                    $htmlDoc = $this->getHtmlContainer($doc);
                    $container->add($htmlDoc);
                }

                return $container->render();

            } else {
                return '';
            }
        }


        $flag = false;
        if (array_key_exists('link', $this->query)) {
            $flag = true;
        }

        $docId = (int)$this->path;
        $doc = new Document($docId);
        if (!$doc->loaded()) {
            return '';
        }

        if ($flag) {
            return link_to('doc_download', array('id' => $doc->pk()));
        }

        return $this->getHtmlContainer($doc)->render();
    }

    /**
     * @param Document $doc
     * @return Html
     * @throws \Delorius\Exception\Error
     */
    protected function getHtmlLink(Document $doc)
    {
        $a = Html::el('a');
        $a->href(link_to('doc_download', array('id' => $doc->pk(), 'hash' => $doc->code)));
        $a->addAttributes(array('title' => ($doc->title ? $doc->title : $doc->file_name()), 'class' => 'b-file__name'));
        $a->setHtml(($doc->title ? $doc->title : $doc->file_name()));
        return $a;
    }

    /**
     * @param Document $doc
     * @return Html
     * @throws \Delorius\Exception\Error
     */
    protected function getHtmlContainer(Document $doc)
    {
        $a = $this->getHtmlLink($doc);
        $container = Html::el('div', array('class' => 'b-file b-file_' . $doc->ext));
        $container->setHtml(_sf('{0}<span class="b-file__size">{1}</span>', $a, $doc->file_size()));
        return $container;
    }

}