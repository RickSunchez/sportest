<?php

namespace CMS\Core\Component\Snippet\Controls;

use CMS\Core\Component\Snippet\AParserRenderer;
use CMS\Core\Entity\Article;
use Delorius\Utils\Strings;
use Delorius\View\Html;
use Delorius\View\View;

class ArticleTag extends AParserRenderer
{
    public function render()
    {


        if ($this->path == 'list' && array_key_exists('id', $this->query)) {
            $ids = explode(',', $this->query['id']);

            if (count($ids) == 0) {
                return '';
            }

            $articles = Article::model()->sort()->active()->where('id', 'in', $ids)->find_all();
            $var = array();
            $var['articles'] = $articles;
            $view = new View();
            $html = $view->load('cms/article/_list_snippet', $var);
            return $html;

        }

        $article = Article::model($this->path);
        if (!$article->loaded()) {
            return '';
        }

        if (array_key_exists('link', $this->query)) {
            return $article->link();
        }

        $a = Html::el('a');
        $a->href($article->link());
        $a->addAttributes(array('title' => Strings::escape($article->name), 'class' => 'b-link b-link_article b-article__link_' . $article->pk()));
        $a->setText($article->name);
        return $a->render();
    }


}