<?php
namespace Delorius\Configure\File;

use Delorius\Core\Object;
use Delorius\Exception\Error;
use Delorius\Tools\Debug\Profiler;
use Delorius\Utils\Arrays;
use Delorius\Utils\FileSystem;

Class Config extends Object
{
    const DEFAULT_CONFIG_INSTANCE = '.';
    protected $dir;
    protected $key;
    protected $exp;
    protected $data = array();
    protected $is_edit = false;
    protected $is_read = false;

    public function __construct($path, $key, $exp = 'neon')
    {
        $this->dir = $path;
        $this->key = $key;
        $this->exp = $exp;
    }

    /**
     * @param $key
     * @param string $exp
     * @return Config
     */
    public function deliver($key, $exp = 'php')
    {
        return new self($this->dir, $key, $exp);
    }

    protected function readFile()
    {
        if (!$this->is_read) {
            $loader = new \Delorius\DI\Config\Loader;
            if (!file_exists($this->config_file())) {
                FileSystem::write($this->config_file(), ' ');
            }
            $this->data = (array)$loader->load($this->config_file());
            $this->is_read = true;
        }
    }

    public function set($name, $value = null)
    {
        $token = Profiler::start('File Config', _sf('set({0})', $name));

        $this->readFile();
        $this->data = Arrays::set($this->data, $name, $value);
        $this->is_edit = true;

        if (isset($token)) {
            Profiler::stop($token);
        }
        return $this;
    }

    public function delete($name)
    {
        $this->readFile();
        $this->data = Arrays::remove($this->data, $name);
        $this->is_edit = true;
        return $this;
    }

    public function get($name, $default = null)
    {
        $token = Profiler::start('File Config', _sf('get({0})', $name));

        $this->readFile();
        $cfg = Arrays::get($this->data, $name, $default);

        if (isset($token)) {
            Profiler::stop($token);
        }
        return $cfg;
    }

    public function save()
    {
        if ($this->is_edit)
            $this->write($this->data);
    }

    public function deleteFile()
    {
        FileSystem::delete($this->config_file());
    }

    private function write($data)
    {
        try {
            $loader = new \Delorius\DI\Config\Loader;
            $loader->save($data, $this->config_file());
            $this->is_edit = false;
            return true;
        } catch (Error $e) {
            return false;
        }
    }

    protected function config_file()
    {
        $file = $this->getNameFile($this->key);
        return $this->dir . '/' . $file;
    }

    protected function getNameFile($key)
    {
        return $key . '.config.' . $this->exp;
    }

    function __destruct()
    {
        $this->save();
    }


}