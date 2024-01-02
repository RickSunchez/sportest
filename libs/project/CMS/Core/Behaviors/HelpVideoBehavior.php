<?php
namespace CMS\Core\Behaviors;

use Delorius\Behaviors\ORMBehavior;
use Delorius\Core\ORM;
use RicardoFiorani\Matcher\VideoServiceMatcher;

class HelpVideoBehavior extends ORMBehavior
{

    public function beforeSave(ORM $orm)
    {
        if (!$orm->loaded() || $orm->changed('url')) {
            $vsm = new VideoServiceMatcher();
            $video = $vsm->parse($orm->url);
            if ($video->isEmbeddable()) {
                if (!$orm->name) {
                    $orm->name = $video->getServiceName();
                }
            }
        }
    }

    /**
     * @param int $width
     * @param int $height
     * @param bool|false $autoplay
     * @param bool|false $secure
     * @return string
     * @throws \RicardoFiorani\Exception\ServiceNotAvailableException
     */
    public function getEmbedCode($width, $height, $autoplay = false, $secure = false)
    {
        if (!$this->getOwner()->url) {
            return '';
        }

        $vsm = new VideoServiceMatcher();
        $video = $vsm->parse($this->getOwner()->url);
        return $video->getEmbedCode($width, $height, $autoplay, $secure);
    }

} 