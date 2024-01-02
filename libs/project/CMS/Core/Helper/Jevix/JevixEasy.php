<?php
namespace CMS\Core\Helper\Jevix;

/* парсер для сообщений */
class JevixEasy extends AJevix {
    
    /**
     * Конфигурирует типограф
     *
     */
    public function JevixConfig() {
        // разрешаем в параметрах символ &
        unset($this->oJevix->entities1['&']);
        // Разрешённые теги
        $this->oJevix->cfgAllowTags(array('a', 'i', 'b', 'u', 's', 'em', 'strong', 'nobr', 'li', 'ol', 'ul', 'sup', 'abbr', 'sub', 'acronym', 'br', 'hr', 'pre', 'code', 'blockquote', 'table', 'tr', 'td', 'th', 'tbody', 'thead', 'tfoot', 'p', 'div'));
        // Коротие теги типа
        $this->oJevix->cfgSetTagShort(array('br', 'hr'));
        // Разрешённые параметры тегов	
        $this->oJevix->cfgAllowTagParams('a', array('title', 'href', 'rel'));
        $this->oJevix->cfgAllowTagParams('td', array('colspan', 'rowspan', 'align'));
        $this->oJevix->cfgAllowTagParams('table', array('border', 'rules', 'rel'));
        $this->oJevix->cfgSetTagParamsRequired('a', 'href');
        // Теги которые необходимо вырезать из текста вместе с контентом
        $this->oJevix->cfgSetTagCutWithContent(array('script', 'iframe', 'style'));
        // Вложенные теги
        $this->oJevix->cfgSetTagChilds('ul', array('li'), false, true);
        $this->oJevix->cfgSetTagChilds('ol', array('li'), false, true);
        // Не нужна авто-расстановка <br>
        $this->oJevix->cfgSetTagNoAutoBr(array('ul', 'ol', 'table', 'tr', 'td', 'th'));
        if (self::NOINDEX) {
            $this->oJevix->cfgSetTagParamDefault('a', 'rel', 'nofollow', true);
        }
        // Отключение авто-добавления <br>
        $this->oJevix->cfgSetAutoBrMode(true);
        // Автозамена
        $this->oJevix->cfgSetAutoReplace(array('+/-', '(c)', '(с)', '(r)', '(C)', '(С)', '(R)'), array('±', '©', '©', '®', '©', '©', '®'));
        $this->oJevix->cfgSetAutoLinkMode(true);
    }     

}

?>