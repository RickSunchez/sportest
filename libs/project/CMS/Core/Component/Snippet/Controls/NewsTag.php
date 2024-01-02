<?php
namespace CMS\Core\Component\Snippet\Controls;

use CMS\Core\Component\Snippet\AParserRenderer;
use CMS\Core\Entity\News;
use Delorius\Utils\Strings;
use Delorius\View\Html;

class NewsTag extends AParserRenderer
{
    public function render()
    {
        if (array_key_exists('link', $this->query)) {
            $news = News::model($this->path);
            if ($news->loaded())
                return $news->link();
            else {
                return '';
            }
        }
        $news = News::model($this->path);
        if ($news->loaded()) {
            $a = Html::el('a');
            $a->href($news->link());
            $a->addAttributes(array('title' => Strings::escape($news->name), 'class' => 'b-link b-link_news b-news__link_' . $news->pk()));
            $a->setText($news->name);
            return $a->render();
        }
        return '';
    }


}