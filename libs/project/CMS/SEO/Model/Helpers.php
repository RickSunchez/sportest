<?php
namespace CMS\SEO\Model;

use CMS\SEO\Entity\Template;
use Delorius\Core\Environment;
use Delorius\Exception\Error;
use Delorius\Utils\Strings;

class Helpers
{

    public static $lifetime = '+ 20 days';

    /** for by ->getTemplates() */
    protected static $_temp = array();
    protected static $_texts = array();
    protected static $_max = 20;
    /** for by ->getRndTemplate() */
    protected static $_cache_orm = array();

    /**
     * @param $id
     * @return void
     */
    public static function clean($id)
    {
        $cache = self::getCache($id);
        $cache->delete('texts');
    }

    /**
     * @param $id
     * @return void
     */
    public static function delete($id)
    {
        $cache = self::getCache($id);
        $cache->deleteFile();
    }

    /**
     * @param $id
     * @return array|mixed|NULL
     */
    public static function getTemplates($id)
    {

        $cache = self::getCache($id);
        $texts = $cache->get('texts');

        if (!count($texts)) {

            $orm = new Template($id);
            if (!$orm->loaded() || !$orm->text) {
                return array();
            }


            try {
                for ($i = 1; $i <= $orm->count; $i++) {

                    self::$_max = $orm->step;
                    $text = self::textGenerator($orm->text);
                    self::$_texts[$i] = $text;
                }

            } catch (Error $e) {

            }


            $cache->set('texts', self::$_texts);
            $texts = self::$_texts;


            self::$_temp = array();
            self::$_texts = array();

        }
        return $texts;
    }


    /**
     * @param $id
     * @return string
     */
    public static function getRndTemplate($id, $data = null)
    {
        if (!isset(self::$_cache_orm[$id])) {
            self::$_cache_orm[$id] = new Template($id);
        }

        if (!self::$_cache_orm[$id]->text) {
            return null;
        }

        $text = Strings::textGenerator(self::$_cache_orm[$id]->text);
        return $text;
    }

    /**
     * @param int $id
     * @param null|int $ownerId
     * @param null $data [name=>value] by text {name}
     * @return string
     * @throws \Delorius\Exception\Error
     */
    public static function getText($id, $ownerId = null, $data = null)
    {
        if ($ownerId) {
            $texts = self::getTemplates($id);
            $count = count($texts);
            if ($count == 0) {
                return '';
            }
            $current = ($ownerId % $count);
            if ($current == 0) {
                $current = $count;
            }
            $text = $texts[$current];
        } else {
            $text = self::getRndTemplate($id);
        }

        $text = self::parserText($text, $data);
        $text = Environment::getContext()->getCloneService('parser')->html($text);
        return _sf('<!--gen id:{0};own:{1}--!>', $id, $ownerId) . $text;
    }

    /**
     * @param string $text
     * @param null $data
     * @return string
     * @throws Error
     */
    public static function parserText($text, $data = null)
    {
        $pattern = array();
        if (is_array($data) && count($data)) {
            $pattern = array();
            foreach ($data as $name => $value) {
                $pattern['#\{' . $name . '\}#'] = $value;
            }
        }

        $pattern['#\{\+#'] = '[';
        $pattern['#\+\}#'] = ']';
        $text = Strings::replace($text, $pattern);
        return $text;
    }

    /**
     * @param int $id
     * @return \Delorius\Configure\File\Config
     * @throws \Delorius\Exception\Error
     */
    protected static function getCache($id)
    {
        return Environment::getContext()->getService('configFile')->deliver('orm.' . $id);
    }

    /**
     * @param $tmp
     * @return string
     * @throws Error
     */
    protected static function textGenerator($tmp)
    {

        self::$_max--;
        $text = Strings::textGenerator($tmp);
        if (!self::$_temp[md5($text)]) {
            self::$_temp[md5($text)] = true;
            return $text;
        }

        if (self::$_max == 0) {
            throw new Error('Конец попыток');
        }

        return self::textGenerator($tmp);
    }

}