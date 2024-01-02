<?php
namespace Delorius\Tools;

use Delorius\Core\Object;
use Delorius\Http\IRequest;
use Delorius\Utils\FileSystem;
use Delorius\Utils\Finder;
use Delorius\Utils\Size;
use Delorius\Utils\Strings;


class LoggerFiles extends Object implements ILogger
{
    const FILE_SIZE = 1048576; //1mb

    /**
     * @var \Delorius\Http\IRequest
     */
    protected $httpRequest;
    /**
     * Пусть до log file
     * @var string
     */
    private $_filepath;

    /**
     * @var string
     */
    private $_dir;

    /**
     *  Lock file mode - default <code>null</code>. If Lock mode <code>null</code>, then file doesn't lock. You may get any
     *  lock mode. For example: <code>LOCK_SH</code>, or <code>LOCK_EX</code>, or <code>LOCK_EX | LOCK_NB</code>, etc.
     *  If you decided include locking, remember: "if file was locking, your script will be wait,
     *  when some process unlock file".
     * @var        int
     * @access     private
     */
    private $_lock;

    public function __construct($path, IRequest $request)
    {
        $this->httpRequest = $request;
        $this->_dir = $path;
        $this->_filepath = $path . '/stat.log';
        $this->checkPath();
    }

    public function info($message, $status)
    {
        $this->log($message, $status, ILogger::INFO);
    }

    public function warning($message, $status)
    {
        $this->log($message, $status, ILogger::WARNING);
    }

    public function error($message, $status)
    {
        $this->log($message, $status, ILogger::ERROR);
    }

    public function alert($message, $status)
    {
        $this->log($message, $status, ILogger::ALERT);
    }

    public function debug($message, $status)
    {
        $this->log($message, $status, ILogger::DEBUG);
    }

    public function critical($message, $status)
    {
        $this->log($message, $status, ILogger::CRITICAL);
    }


    protected function write($text)
    {
        $fh = fopen($this->_filepath, 'a') or die('Error. Can\'t open file:' . $this->_filepath);
        if (!isset($this->_lock) or $this->lock($fh, $this->_lock)) {
            fwrite($fh, $text);
            if (!isset($this->_lock)) $this->lock($fh, LOCK_UN);
        }
        fclose($fh) or die('Error. Can\'t close file: ' . $this->_filepath);
    }

    protected function lock($fh, $mode)
    {
        if (stristr(php_uname(), 'Windows 9')) return true;
        return flock($fh, $mode);
    }

    protected function dump($message, $type)
    {
        if ($type == ILogger::DEBUG) {
            ob_start();
            var_export($message);
            $message = gettype($message) . LOGGER_LINE_FEED . ob_get_contents();
            ob_end_clean();

        } elseif (is_array($message)) {
            if (count($message) != 1) {
                $temp = 'array ' . LOGGER_LINE_FEED;
                foreach ($message as $key => $value) {
                    $temp .= $key . ' : ' . $this->dump($value, $type) . LOGGER_LINE_FEED;
                }
            } else {
                $temp = $message[0] . LOGGER_LINE_FEED;
            }
            $message = $temp;

        } else {
            $message .= LOGGER_LINE_FEED;
        }
        return $message;
    }

    protected function log($message, $status, $type = ILogger::INFO)
    {
        $message = $this->dump($message, $type);
        $data = date('Y-m-d H:i:s');
        $status = Strings::lower($status);
        $type = Strings::lower($type);
        $ip = $this->httpRequest->getRemoteAddress();
        $text = "[$data][$type][$status][$ip] $message";
        $this->write($text);
    }


    /*****************   file watcher ******************/


    protected function checkPath()
    {
        if (file_exists($this->_filepath)) {
            $size = filesize($this->_filepath);
            if ($size > self::FILE_SIZE * 12) {
                $files = Finder::find('*.log*')->in($this->_dir);
                $count = 0;
                foreach ($files as $file) {
                    $count++;
                }
                FileSystem::rename($this->_filepath, $this->_filepath . $count);
            }
        }


    }


}