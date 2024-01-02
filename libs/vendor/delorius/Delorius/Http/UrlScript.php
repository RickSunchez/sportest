<?php
namespace Delorius\Http;

/**
 * Extended HTTP URL.
 *
 * <pre>
 * http://site.org/admin/script.php/pathinfo/?name=param#fragment
 *                \________________/\________/
 *                        |              |
 *                   scriptPath       pathInfo
 * </pre>
 *
 * - scriptPath:  /admin/script.php (or simply /admin/ when script is directory index)
 * - pathInfo:    /pathinfo/ (additional path information)
 *
 * @property   string $scriptPath
 * @property-read string $pathInfo
 */
Class UrlScript extends Url
{
    /** @var string */
    public $scriptPath = '/';

    /**
     * Sets the script-path part of URI.
     * @param  string
     * @return UrlScript  provides a fluent interface
     */
    public function setScriptPath($value)
    {
        $this->scriptPath = (string)$value;
        return $this;
    }


    /**
     * Returns the script-path part of URI.
     * @return string
     */
    public function getScriptPath()
    {
        return $this->scriptPath;
    }

    /**
     * Returns the additional path information.
     * @return string
     */
    public function getPathInfo()
    {
        if(strlen($this->scriptPath) <= 1)
            return $this->path;
        else
            return (string)substr($this->path, strlen($this->scriptPath));
    }


    public function getAuthorityInfo()
    {
        return (string)$this->getAuthority() . $this->getScriptPath();
    }
}
