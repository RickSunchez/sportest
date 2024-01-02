<?php
namespace Delorius\View;

use Delorius\Exception\Error;
use Delorius\Iterators\Recursor;
use Delorius\Utils\Json;
use Delorius\Utils\Strings;

/**
 * HTML helper.
 *
 * <code>
 * $anchor = Html::el('a')->href($link)->setText('Delorius');
 * $el->class = 'my_class';
 * echo $el;
 *
 * echo $el->startTag(), $el->endTag();
 * </code>
 */

Class Html {
    /*
     * Шаблон сайта 
     * 
     */

    const SITE_PAGINATOR = 'paginator';

    /*
     * <title>...</title>
     */
    const META_TITLE = 'TITLE';

    /*
     * <META NAME="KEYWORDS" CONTENT="Ключевые слова, разделенные запятой, до 1000 символов">
     */
    const META_NAME_KEY = 'KEYWORDS';

    /*
     * <meta name="author" content="Monaxxx ahalyapov@gmail.com" />
     */
    const META_NAME_AUTHOR = 'AUTHOR';

    /*
     * <META NAME="DESCRIPTION" CONTENT="Описание данного документа, до 100 символов"> 
     */
    const META_NAME_DESC = 'DESCRIPTION';

    /*
     * <META NAME="URL" CONTENT="http://www.microsoft.com">
     */
    const META_NAME_URL = 'URL';

    /*
     * <META NAME="ROBOTS" CONTENT="INDEX,NOFOLLOW"> 
     * Возможные варианты:
      a) INDEX - возможность индексирования данного документа (иначе NOINDEX)
      б) FOLLOW - возможность индексирования всех документов, на которые есть
     *     ссылки в данном HTML файле (иначе NOFOLLOW)
      в) ALL - одновременное выполнение условий INDEX и FOLLOW
      г) NONE - одновременное выполнение условий NOINDEX и NOFOLLOW
     */
    const META_NAME_ROBOTS = 'ROBOTS';
    /* vars robots */
    const META_ROBOTS_INDEX = 'INDEX';
    const META_ROBOTS_NOINDEX = 'NOINDEX';
    const META_ROBOTS_FOLLOW = 'FOLLOW';
    const META_ROBOTS_NOFOLLOW = 'FOLLOW';
    const META_ROBOTS_ALL = 'ALL';
    const META_ROBOTS_NONE = 'NONE';


    /*
     * <META NAME="DOCUMENT-STATE" CONTENT="STATIC">
     * Данный тэг управляет частотой индексации и может принимать два значения: 
     * STATIC (документ статичен, то есть не меняется, и, следовательно, 
     * индексировать его нужно только один раз) и 
     * DYNAMIC (для часто изменяющися документов, которые нужно реиндексировать)
     */
    const META_NAME_DOCUMENT_STATE = 'DOCUMENT-STATE';

    /*
     * <META HTTP-EQUIV="EXPIRES" CONTENT="Wed, 26 Feb 1997 08:21:57 GMT">
     * Формат даты: RFC850
     */
    const META_HTTP_EXPIRES = 'EXPIRES';

    /*
     * <META HTTP-EQUIV="PRAGMA" CONTENT="NO-CACHE">
     * Возможно одно значение NO-CACHE, 
     * то есть данный документ не кэшируется броузером.
     */
    const META_HTTP_PRAGMA = 'PRAGMA';

    /* HTML 4.01:
     * <meta http-equiv="content-type" content="text/html; charset=UTF-8" />
     * HTML5
     * <meta charset="UTF-8">
     */
    const META_HTTP_CONTENT_TYPE = 'CONTENT-TYPE';

    /*
     * <meta http-equiv="refresh" content="30" />
     * Определяет интервал времени для документа, чтобы обновить себя.
     */
    const META_HTTP_REFRESH = 'REFRESH';


    /** @var string  element's name */
    private $name;

    /** @var bool  is element empty? */
    private $isEmpty;

    /** @var array  element's attributes */
    public $attrs = array();

    /** @var array  of Html | string nodes */
    protected $children = array();

    /** @var bool  use XHTML syntax? */
    public static $xhtml = TRUE;

    /** @var array  empty elements */
    public static $emptyElements = array('img'=>1,'hr'=>1,'br'=>1,'input'=>1,'meta'=>1,'area'=>1,'embed'=>1,'keygen'=>1,
        'source'=>1,'base'=>1,'col'=>1,'link'=>1,'param'=>1,'basefont'=>1,'frame'=>1,'isindex'=>1,'wbr'=>1,'command'=>1);



    /**
     * Static factory.
     * @param  string element name (or NULL)
     * @param  array|string element's attributes (or textual content)
     * @return Html
     */
    public static function el($name = NULL, $attrs = NULL)
    {
        $el = new static;
        $parts = explode(' ', $name, 2);
        $el->setName($parts[0]);

        if (is_array($attrs)) {
            $el->attrs = $attrs;

        } elseif ($attrs !== NULL) {
            $el->setText($attrs);
        }

        if (isset($parts[1])) {
            foreach (Strings::matchAll($parts[1] . ' ', '#([a-z0-9:-]+)(?:=(["\'])?(.*?)(?(2)\\2|\s))?#i') as $m) {
                $el->attrs[$m[1]] = isset($m[3]) ? $m[3] : TRUE;
            }
        }

        return $el;
    }



    /**
     * Changes element's name.
     * @param  string
     * @param  bool  Is element empty?
     * @return Html  provides a fluent interface
     * @throws Error
     */
    final public function setName($name, $isEmpty = NULL)
    {
        if ($name !== NULL && !is_string($name)) {
            throw new Error("Name must be string or NULL, " . gettype($name) ." given.");
        }

        $this->name = $name;
        $this->isEmpty = $isEmpty === NULL ? isset(static::$emptyElements[$name]) : (bool) $isEmpty;
        return $this;
    }



    /**
     * Returns element's name.
     * @return string
     */
    final public function getName()
    {
        return $this->name;
    }



    /**
     * Is element empty?
     * @return bool
     */
    final public function isEmpty()
    {
        return $this->isEmpty;
    }



    /**
     * Sets multiple attributes.
     * @param  array
     * @return Html  provides a fluent interface
     */
    public function addAttributes(array $attrs)
    {
        $this->attrs = $attrs + $this->attrs;
        return $this;
    }



    /**
     * Overloaded setter for element's attribute.
     * @param  string    HTML attribute name
     * @param  mixed     HTML attribute value
     * @return void
     */
    final public function __set($name, $value)
    {
        $this->attrs[$name] = $value;
    }



    /**
     * Overloaded getter for element's attribute.
     * @param  string    HTML attribute name
     * @return mixed     HTML attribute value
     */
    final public function &__get($name)
    {
        return $this->attrs[$name];
    }



    /**
     * Overloaded tester for element's attribute.
     * @param  string    HTML attribute name
     * @return bool
     */
    final public function __isset($name)
    {
        return isset($this->attrs[$name]);
    }



    /**
     * Overloaded unsetter for element's attribute.
     * @param  string    HTML attribute name
     * @return void
     */
    final public function __unset($name)
    {
        unset($this->attrs[$name]);
    }



    /**
     * Overloaded setter for element's attribute.
     * @param  string  HTML attribute name
     * @param  array   (string) HTML attribute value or pair?
     * @return Html  provides a fluent interface
     */
    final public function __call($m, $args)
    {
        $p = substr($m, 0, 3);
        if ($p === 'get' || $p === 'set' || $p === 'add') {
            $m = substr($m, 3);
            $m[0] = $m[0] | "\x20";
            if ($p === 'get') {
                return isset($this->attrs[$m]) ? $this->attrs[$m] : NULL;

            } elseif ($p === 'add') {
                $args[] = TRUE;
            }
        }

        if (count($args) === 0) { // invalid

        } elseif (count($args) === 1) { // set
            $this->attrs[$m] = $args[0];

        } elseif ((string) $args[0] === '') {
            $tmp = & $this->attrs[$m]; // appending empty value? -> ignore, but ensure it exists

        } elseif (!isset($this->attrs[$m]) || is_array($this->attrs[$m])) { // needs array
            $this->attrs[$m][$args[0]] = $args[1];

        } else {
            $this->attrs[$m] = array($this->attrs[$m], $args[0] => $args[1]);
        }

        return $this;
    }



    /**
     * Special setter for element's attribute.
     * @param  string path
     * @param  array query
     * @return Html  provides a fluent interface
     */
    final public function href($path, $query = NULL)
    {
        if ($query) {
            $query = http_build_query($query, NULL, '&');
            if ($query !== '') {
                $path .= '?' . $query;
            }
        }
        $this->attrs['href'] = $path;
        return $this;
    }



    /**
     * Sets element's HTML content.
     * @param  string
     * @return Html  provides a fluent interface
     * @throws Error
     */
    final public function setHtml($html)
    {
        if ($html === NULL) {
            $html = '';

        } elseif (is_array($html)) {
            throw new Error("Textual content must be a scalar, " . gettype($html) ." given.");

        } else {
            $html = (string) $html;
        }

        $this->removeChildren();
        $this->children[] = $html;
        return $this;
    }



    /**
     * Returns element's HTML content.
     * @return string
     */
    final public function getHtml()
    {
        $s = '';
        foreach ($this->children as $child) {
            if (is_object($child)) {
                $s .= $child->render();
            } else {
                $s .= $child;
            }
        }
        return $s;
    }



    /**
     * Sets element's textual content.
     * @param  string
     * @return Html  provides a fluent interface
     * @throws Error
     */
    final public function setText($text)
    {
        if (!is_array($text)) {
            $text = htmlspecialchars((string) $text, ENT_NOQUOTES);
        }
        return $this->setHtml($text);
    }



    /**
     * Returns element's textual content.
     * @return string
     */
    final public function getText()
    {
        return html_entity_decode(strip_tags($this->getHtml()), ENT_QUOTES, 'UTF-8');
    }



    /**
     * Adds new element's child.
     * @param  Html|string child node
     * @return Html  provides a fluent interface
     */
    final public function add($child)
    {
        return $this->insert(NULL, $child);
    }



    /**
     * Creates and adds a new Html child.
     * @param  string  elements's name
     * @param  array|string element's attributes (or textual content)
     * @return Html  created element
     */
    final public function create($name, $attrs = NULL)
    {
        $this->insert(NULL, $child = static::el($name, $attrs));
        return $child;
    }



    /**
     * Inserts child node.
     * @param  int
     * @param  Html node
     * @param  bool
     * @return Html  provides a fluent interface
     * @throws \Exception
     */
    public function insert($index, $child, $replace = FALSE)
    {
        if ($child instanceof Html || is_scalar($child)) {
            if ($index === NULL) { // append
                $this->children[] = $child;

            } else { // insert or replace
                array_splice($this->children, (int) $index, $replace ? 1 : 0, array($child));
            }

        } else {
            throw new Error("Child node must be scalar or Html object, " . (is_object($child) ? get_class($child) : gettype($child)) ." given.");
        }

        return $this;
    }



    /**
     * Inserts (replaces) child node (\ArrayAccess implementation).
     * @param  int
     * @param  Html node
     * @return void
     */
    final public function offsetSet($index, $child)
    {
        $this->insert($index, $child, TRUE);
    }



    /**
     * Returns child node (\ArrayAccess implementation).
     * @param  int index
     * @return mixed
     */
    final public function offsetGet($index)
    {
        return $this->children[$index];
    }



    /**
     * Exists child node? (\ArrayAccess implementation).
     * @param  int index
     * @return bool
     */
    final public function offsetExists($index)
    {
        return isset($this->children[$index]);
    }



    /**
     * Removes child node (\ArrayAccess implementation).
     * @param  int index
     * @return void
     */
    public function offsetUnset($index)
    {
        if (isset($this->children[$index])) {
            array_splice($this->children, (int) $index, 1);
        }
    }



    /**
     * Required by the \Countable interface.
     * @return int
     */
    final public function count()
    {
        return count($this->children);
    }



    /**
     * Removed all children.
     * @return void
     */
    public function removeChildren()
    {
        $this->children = array();
    }



    /**
     * Iterates over a elements.
     * @param  bool    recursive?
     * @param  string  class types filter
     * @return \RecursiveIterator
     */
    final public function getIterator($deep = FALSE)
    {
        if ($deep) {
            $deep = $deep > 0 ? \RecursiveIteratorIterator::SELF_FIRST : \RecursiveIteratorIterator::CHILD_FIRST;
            return new \RecursiveIteratorIterator(new Recursor(new \ArrayIterator($this->children)), $deep);

        } else {
            return new Recursor(new \ArrayIterator($this->children));
        }
    }



    /**
     * Returns all of children.
     * @return array
     */
    final public function getChildren()
    {
        return $this->children;
    }



    /**
     * Renders element's start tag, content and end tag.
     * @param  int indent
     * @return string
     */
    final public function render($indent = NULL)
    {
        $s = $this->startTag();

        if (!$this->isEmpty) {
            // add content
            if ($indent !== NULL) {
                $indent++;
            }
            foreach ($this->children as $child) {
                if (is_object($child)) {
                    $s .= $child->render($indent);
                } else {
                    $s .= $child;
                }
            }

            // add end tag
            $s .= $this->endTag();
        }

        if ($indent !== NULL) {
            return "\n" . str_repeat("\t", $indent - 1) . $s . "\n" . str_repeat("\t", max(0, $indent - 2));
        }
        return $s;
    }



    final public function __toString()
    {
        return $this->render();
    }



    /**
     * Returns element's start tag.
     * @return string
     */
    final public function startTag()
    {
        if ($this->name) {
            return '<' . $this->name . $this->attributes() . (static::$xhtml && $this->isEmpty ? ' />' : '>');

        } else {
            return '';
        }
    }



    /**
     * Returns element's end tag.
     * @return string
     */
    final public function endTag()
    {
        return $this->name && !$this->isEmpty ? '</' . $this->name . '>' : '';
    }



    /**
     * Returns element's attributes.
     * @return string
     */
    final public function attributes()
    {
        if (!is_array($this->attrs)) {
            return '';
        }

        $s = '';
        foreach ($this->attrs as $key => $value) {
            if ($value === NULL || $value === FALSE) {
                continue;

            } elseif ($value === TRUE) {
                if (static::$xhtml) {
                    $s .= ' ' . $key . '="' . $key . '"';
                } else {
                    $s .= ' ' . $key;
                }
                continue;

            } elseif (is_array($value)) {
                if ($key === 'data') { // deprecated
                    foreach ($value as $k => $v) {
                        if ($v !== NULL && $v !== FALSE) {
                            if (is_array($v)) {
                                $v = Json::encode($v);
                            }
                            $q = strpos($v, '"') === FALSE ? '"' : "'";
                            $s .= ' data-' . $k . '='
                                . $q . str_replace(array('&', $q), array('&amp;', $q === '"' ? '&quot;' : '&#39;'), $v)
                                . (strpos($v, '`') !== FALSE && strpbrk($v, '"\'') === FALSE ? ' ' : '')
                                . $q;
                        }
                    }
                    continue;

                } elseif (strncmp($key, 'data-', 5) === 0) {
                    $value = Json::encode($value);

                } else {
                    $tmp = NULL;
                    foreach ($value as $k => $v) {
                        if ($v != NULL) { // intentionally ==, skip NULLs & empty string
                            //  composite 'style' vs. 'others'
                            $tmp[] = $v === TRUE ? $k : (is_string($k) ? $k . ':' . $v : $v);
                        }
                    }
                    if ($tmp === NULL) {
                        continue;
                    }

                    $value = implode($key === 'style' || !strncmp($key, 'on', 2) ? ';' : ' ', $tmp);
                }

            } else {
                $value = (string) $value;
            }

            $q = strpos($value, '"') === FALSE ? '"' : "'";
            $s .= ' ' . $key . '='
                . $q . str_replace(array('&', $q), array('&amp;', $q === '"' ? '&quot;' : '&#39;'), $value)
                . (strpos($value, '`') !== FALSE && strpbrk($value, '"\'') === FALSE ? ' ' : '')
                . $q;
        }

        $s = str_replace('@', '&#64;', $s);
        return $s;
    }

    /**
     * Convert special characters to HTML entities. All untrusted content
     * should be passed through this method to prevent XSS injections.
     *
     *     echo HTML::chars($username);
     *
     * @param   string  $value          string to convert
     * @param   boolean $double_encode  encode existing entities
     * @return  string
     */
    public static function chars($value, $double_encode = TRUE)
    {
        return htmlspecialchars( (string) $value, ENT_QUOTES, 'utf-8', $double_encode);
    }

    /**
     * Convert all applicable characters to HTML entities. All characters
     * that cannot be represented in HTML with the current character set
     * will be converted to entities.
     *
     *     echo HTML::entities($username);
     *
     * @param   string  $value          string to convert
     * @param   boolean $double_encode  encode existing entities
     * @return  string
     */
    public static function entities($value, $double_encode = TRUE)
    {
        return htmlentities( (string) $value, ENT_QUOTES,'utf-8', $double_encode);
    }

    /**
     * Очищает документ , оставля чисто текст
     *
     * @param string $document
     * @return string
     */
    public static function clearTags($document)
    {
        $search = array("'<script[^>]*?>.*?</script>'si", // Вырезает javaScript
            "'<[\/\!]*?[^<>]*?>'si", // Вырезает HTML-теги
            "'([\r\n])[\s]+'", // Вырезает пробельные символы
            "'&(quot|#34);'i", // Заменяет HTML-сущности
            "'&(amp|#38);'i",
            "'&(lt|#60);'i",
            "'&(gt|#62);'i",
            "'&(nbsp|#160);'i",
            "'&(iexcl|#161);'i",
            "'&(cent|#162);'i",
            "'&(pound|#163);'i",
            "'&(copy|#169);'i",
            "'&#(\d+);'"); // интерпретировать как php-код

        $replace = array("",
            "",
            "\\1",
            "\"",
            "&",
            "<",
            ">",
            " ",
            chr(161),
            chr(162),
            chr(163),
            chr(169),
            "chr(\\1)"
        );

        $text = preg_replace($search, $replace, $document);
        return $text;
    }



    /**
     * Clones all children too.
     */
    public function __clone()
    {
        foreach ($this->children as $key => $value) {
            if (is_object($value)) {
                $this->children[$key] = clone $value;
            }
        }
    }


}

