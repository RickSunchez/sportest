<?php
namespace CMS\Core\Component\Snippet\Controls;

use CMS\Core\Component\Snippet\AParserRenderer;

use CMS\Core\Entity\Video;
use Delorius\View\View;

class VideoTag extends AParserRenderer
{
    public function render()
    {
        $videoId = $this->path;

        if (array_key_exists('link', $this->query)) {
            return link_to('video', array('id' => $videoId));
        }

        $video = Video::model($videoId);
        if (!$video->loaded()) {
            return '';
        }

        $view = new View();
        $var['video'] = $video;
        $theme = $this->query['theme'] ? '_' . $this->query['theme'] : '';
        $var['w'] = $this->query['w'] ? $this->query['w'] : '100%';
        $var['h'] = $this->query['h'] ? $this->query['h'] : '430';
        $var['a'] = $this->query['a'] ? $this->query['a'] : false;
        return $view->load('cms/video/_view' . $theme, $var);
    }

}