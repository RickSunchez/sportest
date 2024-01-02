<?php
namespace CMS\Core\Helper\Jevix;

use Delorius\Utils\Strings;

abstract class AJevix {

    /**
     * Объект типографа
     *
     * @var Jevix
     */
    protected $oJevix;

    const NOINDEX = true;   // "прятать" или нет ссылки от поисковиков, оборачивая их в тег <noindex> и добавляя rel="nofollow"

    /**
     * Инициализация модуля
     *
     */

    protected function __construct() {
        /**
         * Создаем объект типографа и запускаем его конфигурацию
         */
        $this->oJevix = new \Jevix();
        $this->JevixConfig();
    }

    /**
     * Парсит текст
     *
     * @param string $sText
     */
    public static function Parser($sText) {
        $class = get_called_class() ;
        $self = new $class;
        
        $sText =  Strings::unescape($sText);
        $sText =  $self->FlashParamParser($sText);
        $sText =  $self->JevixParser($sText);
        $sText =  $self->VideoParser($sText);        
        $sText =  $self->CodeSourceParser($sText);
        
        return $sText;
    }

    /**
     * Конфигурирует типограф
     * some config jevix
     */
    abstract function JevixConfig(); 
    

    /**
     * Парсинг текста с помощью Jevix
     *
     * @param string $sText
     * @param array $aError
     * @return string
     */
    public function JevixParser($sText, &$aError = null) {
        $sResult = $this->oJevix->parse($sText, $aError);
        return $sResult;
    }

    /**
     * Парсинг текста на предмет видео
     * <video>{url}</video>
     *
     * @param string $sText
     * @return string
     */
    public function VideoParser($sText) {
        /**
         * youtube.com
         */
        $sText = preg_replace('/<video>http:\/\/(?:www\.|)youtube\.com\/watch\?v=([a-zA-Z0-9_\-]+)(&.+)?<\/video>/Ui', '<iframe width="560" height="315" src="http://www.youtube.com/embed/$1" frameborder="0" allowfullscreen></iframe>', $sText);
        /**
         * vimeo.com
         */
        $sText = preg_replace('/<video>http:\/\/(?:www\.|)vimeo\.com\/(\d+).*<\/video>/i', '<iframe src="http://player.vimeo.com/video/$1" width="500" height="281" frameborder="0" webkitAllowFullScreen mozallowfullscreen allowFullScreen></iframe>', $sText);
        /**
         * rutube.ru
         */
        $sText = preg_replace('/<video>http:\/\/(?:www\.|)rutube\.ru\/tracks\/(\d+)\.html.*<\/video>/Ui', '<OBJECT width="470" height="353"><PARAM name="movie" value="http://video.rutube.ru/$1"></PARAM><PARAM name="wmode" value="window"></PARAM><PARAM name="allowFullScreen" value="true"></PARAM><EMBED src="http://video.rutube.ru/$1" type="application/x-shockwave-flash" wmode="window" width="470" height="353" allowFullScreen="true" ></EMBED></OBJECT>', $sText);
        /**
         * video.yandex.ru
         */
        $sText = preg_replace('/<video>http:\/\/video\.yandex\.ru\/users\/([a-zA-Z0-9_\-]+)\/view\/(\d+).*<\/video>/i', '<object width="467" height="345"><param name="video" value="http://video.yandex.ru/users/$1/view/$2/get-object-by-url/redirect"></param><param name="allowFullScreen" value="true"></param><param name="scale" value="noscale"></param><embed src="http://video.yandex.ru/users/$1/view/$2/get-object-by-url/redirect" type="application/x-shockwave-flash" width="467" height="345" allowFullScreen="true" scale="noscale" ></embed></object>', $sText);
        return $sText;
    }

    /**
     * Заменяет все вхождения короткого тега <param/> на длиную версию <param></param>
     * Заменяет все вхождения короткого тега <embed/> на длиную версию <embed></embed>
     *
     * @param string $sText Исходный текст
     * @return string
     */
    protected function FlashParamParser($sText) {
        if (preg_match_all("@(<\s*param\s*name\s*=\s*(?:\"|').*(?:\"|')\s*value\s*=\s*(?:\"|').*(?:\"|'))\s*/?\s*>(?!</param>)@Ui", $sText, $aMatch)) {
            foreach ($aMatch[1] as $key => $str) {
                $str_new = $str . '></param>';
                $sText = str_replace($aMatch[0][$key], $str_new, $sText);
            }
        }
        if (preg_match_all("@(<\s*embed\s*.*)\s*/?\s*>(?!</embed>)@Ui", $sText, $aMatch)) {
            foreach ($aMatch[1] as $key => $str) {
                $str_new = $str . '></embed>';
                $sText = str_replace($aMatch[0][$key], $str_new, $sText);
            }
        }
        /**
         * Удаляем все <param name="wmode" value="*"></param>
         */
        if (preg_match_all("@(<param\s.*name=(?:\"|')wmode(?:\"|').*>\s*</param>)@Ui", $sText, $aMatch)) {
            foreach ($aMatch[1] as $key => $str) {
                $sText = str_replace($aMatch[0][$key], '', $sText);
            }
        }
        /**
         * А теперь после <object> добавляем <param name="wmode" value="opaque"></param>
         * Решение не фантан, но главное работает :)
         */
        if (preg_match_all("@(<object\s.*>)@Ui", $sText, $aMatch)) {
            foreach ($aMatch[1] as $key => $str) {
                $sText = str_replace($aMatch[0][$key], $aMatch[0][$key] . '<param name="wmode" value="opaque"></param>', $sText);
            }
        }
        return $sText;
    }

    public function CodeSourceParser($sText) {
        $sText = str_replace("<code>", '<pre class="prettyprint"><code>', $sText);
        $sText = str_replace("</code>", '</code></pre>', $sText);
        return $sText;
    }

    /**
     * Производить резрезание текста по тегу <cut>.
     * Возвращаем массив вида:
     * array(
     * 		$sTextShort - текст до тега <cut>
     * 		$sTextNew   - весь текст за исключением удаленного тега
     * 		$sTextCut   - именованное значение <cut> 
     * )
     *
     * @param  string $sText
     * @return array
     */
    public function Cut($sText) {
        $sTextShort = $sText;
        $sTextNew = $sText;
        $sTextCut = null;

        $sTextTemp = str_replace("\r\n", '[<rn>]', $sText);
        $sTextTemp = str_replace("\n", '[<n>]', $sTextTemp);

        if (preg_match("/^(.*)<cut(.*)>(.*)$/Ui", $sTextTemp, $aMatch)) {
            $aMatch['1'] = str_replace('[<rn>]', "\r\n", $aMatch['1']);
            $aMatch['1'] = str_replace('[<n>]', "\r\n", $aMatch['1']);
            $aMatch['3'] = str_replace('[<rn>]', "\r\n", $aMatch['3']);
            $aMatch['3'] = str_replace('[<n>]', "\r\n", $aMatch['3']);
            $sTextShort = $aMatch[1];
            $sTextNew = $aMatch[1] . ' ' . $aMatch[3];
            if (preg_match('/^\s*name\s*=\s*"(.+)"\s*\/?$/Ui', $aMatch[2], $aMatchCut)) {
                $sTextCut = trim($aMatchCut[1]);
            }
        }

        return array($sTextShort, $sTextNew, $sTextCut);
    }

}

?>