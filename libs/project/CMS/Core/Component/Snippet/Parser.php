<?php
namespace CMS\Core\Component\Snippet;

use Delorius\Exception\Error;
use Delorius\Core\Object;

/*
 * Tamplate snippet code
 *
 * v1 = [[{name}:{value}?papam=value&papam2=value2&papam3=value3&papam4=value4]]
 * v2 = [{name}:{value}?papam=value&papam2=value2&papam3=value3&papam4=value4]
 *
 * protected function {name}Tag()
 *
 */

class Parser extends Object
{
    const PATTERN = '\[\[([a-z0-9_]+):([:a-z0-9_\\\?\&\=\.\,]+)\]\]';
    const PATTERN2 = '\[([a-z0-9_]+):([:a-z0-9_\\\?\&\=\.\,]+)\]';

    protected $html;
    protected $query = array();
    protected $name;
    protected $path;
    /** @var  bool */
    protected $_compress;

    /**
     * @var array
     */
    protected $snippets = array();

    /**
     * @param $html
     * @param bool|true $clean
     * @param bool|true $compress
     * @return array|string
     */
    public function html($html, $compress = true)
    {
        $this->_compress = $compress;
        if (empty($html) && !is_scalar($html)) {
            return '';
        } elseif (is_array($html)) {
            $new = array();
            foreach ($html as $k => $v) {
                $new[$this->html($k, $compress)] = $this->html($v, $compress);
            }
            $html = $new;
        } elseif (is_object($html)) {
            $htmls = get_object_vars($html);
            foreach ($htmls as $m => $v) {
                $html->$m = $this->html($v, $compress);
            }
        } elseif (is_string($html)) {
            $html = $this->strParser($html);
        }
        return $html;
    }

    /**
     * @param $code
     * @return string
     */
    public function code($code)
    {
        $this->setHtml($code);
        $this->searchSnippets();
        return $this->getHtml();
    }

    /**
     * @param $html
     * @return $this
     */
    public function setHtml($html)
    {
        $this->html = $html;
        return $this;
    }

    /**
     * @return string
     */
    public function getHtml()
    {
        return (string)$this->html;
    }

    /**
     * @param $name
     * @param $class
     * @return $this
     */
    public function addSnippet($name, $class)
    {
        $this->snippets[$name] = $class;
        return $this;
    }

    /**
     * @param $name
     * @return bool
     */
    public function hasSnippet($name)
    {
        return isset($this->snippets[$name]);
    }

    /**
     * @param $name
     * @return mixed
     */
    public function getSnippet($name)
    {
        return $this->snippets[$name];
    }

    protected function searchSnippets()
    {
        $this->html = preg_replace_callback(_sf('~{0}|{1}~is', self::PATTERN, self::PATTERN2), array($this, 'parserSnippets'), $this->html);
        return $this;
    }

    protected function parserSnippets($result)
    {
        //todo: fix for PATTERN2
        if ($result[1] == '' && $result[2] == '') {
            $result[1] = $result[3];
            $result[2] = $result[4];
        }

        try {
            if (!$this->hasSnippet($result[1])) {
                throw new Error('Absent snippet ' . $result[1]);
            }
            $params = $this->parserParamQuery($result[2]);

            $class = $this->getSnippet($result[1]);
            if (!class_exists($class)) {
                throw new Error('Not exists class ' . $class);
            }

            /** @var AParserRenderer $parser */
            $parser = new $class($params['path'], (array)$params['query']);
            $parser->before();
            return $parser->render();

        } catch (Error $e) {
            logger($e->getMessage(), 'parserSnippets');
            return '';
        }
    }

    /*
     * @var array ('path'=> (string) ,'query'=> (array) )
     */
    protected function parserParamQuery($params)
    {
        list($path, $query) = explode('?', $params);
        $result = array();
        $result['path'] = $path;
        if ($query !== null)
            $query = explode("&", $query);
        if (is_array($query)) {
            foreach ($query as $k => $p) {
                list($name, $value) = explode("=", $p);
                $result['query'][$name] = $value;
            }
        }
        return $result;
    }

    /**
     * @param $html
     * @return string
     */
    protected function strParser($html)
    {
        $this->setHtml($html);

        $this->clearBefore();
        $this->searchSnippets();
        $this->clearAfter();

        $this->htmlCompress();
        return $this->getHtml();
    }


    protected function clearBefore()
    {
        $text = $this->html;
        $text = preg_replace_callback('!(<script.*?>)(.*?)(</script>)!is', array($this, 'cleanHtml'), $text);
        $text = preg_replace_callback('!(textarea<.*?>)(.*?)(</textarea>)!is', array($this, 'cleanHtml'), $text);
        $text = $this->cleanTagRemove($text); // delete [remove]
        $text = $this->cleanTagRandom($text); // generation [random]
        $this->html = $text;
    }

    protected function clearAfter()
    {
        $this->html = preg_replace_callback('!\[html_base64\](.*?)\[\/html_base64\]!is', array($this, 'cleanHtmlAfter'), $this->html);
    }

    /**
     * [random name=123 id=321]...[/random]
     * name- уникальный, используется для кеширования комбинации, обезателен
     * id - конкретная комбинация, не обезателен
     * @param $matches
     * @return mixed
     */
    protected function cleanTagRandom($matches)
    {
        //todo: организовать генеатор случай камбинаций и кешировать даные
        $matches = preg_replace('!\[random(.*?)\](.*?)\[\/random\]!is', '', $matches);
        return $matches;
    }

    protected function cleanTagRemove($matches)
    {
        $matches = preg_replace('!\[remove\](.*?)\[\/remove\]!is', '', $matches);
        return $matches;
    }

    protected function cleanHtml($matches)
    {
        $text = trim($matches[2]);
        $text = $matches[1] . '[html_base64]' . base64_encode($text) . '[/html_base64]' . $matches[3];

        return $text;
    }

    protected function cleanHtmlAfter($matches)
    {
        return base64_decode($matches[1]);
    }

    /**
     * Сжатие текста
     * @param $text
     * @return mixed
     */
    protected function htmlCompress()
    {
        if (DEBUG_MODE) {
            return;
        }

        if (!$this->_compress) {
            return;
        }

        # защищенный текст
        $text = preg_replace_callback('!(<textarea.*?>)(.*?)(</textarea>)!is', array($this, 'cleanHtml'), $this->html);

        // comment delete
        $text = preg_replace('/<!--(.*?)-->/', '', $text);

        // clear /s
        $text = str_replace(array("\r\n", "\r"), "\n", $text);
        $text = str_replace("\t", ' ', $text);
        $text = str_replace("\n   ", "\n", $text);
        $text = str_replace("\n  ", "\n", $text);
        $text = str_replace("\n ", "\n", $text);
        $text = str_replace("\n", ' ', $text);
        $text = str_replace('   ', ' ', $text);
        $text = str_replace('  ', ' ', $text);

        // специфичные замены
        $text = str_replace('>   <', '><', $text);
        $text = str_replace('>  <', '><', $text);
        $text = str_replace('> <', '><', $text);


        $this->html = preg_replace_callback('!\[html_base64\](.*?)\[\/html_base64\]!is', array($this, 'cleanHtmlAfter'), $text);
    }

}