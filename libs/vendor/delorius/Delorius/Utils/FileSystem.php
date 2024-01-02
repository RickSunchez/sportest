<?php
namespace Delorius\Utils;

use Delorius\Exception\Error;

class FileSystem
{

    /**
     * Creates a directory.
     * @return bool
     */
    public static function createDir($dir, $mode = 0777)
    {
        if (!@mkdir($dir, $mode, TRUE)) { // intentionally @; not atomic
            return false;
        }

        return true;
    }


    /**
     * Copies a file or directory.
     * @return void
     */
    public static function copy($source, $dest, $overwrite = TRUE)
    {
        if (stream_is_local($source) && !file_exists($source)) {
            throw new Error("File or directory '$source' not found.");

        } elseif (!$overwrite && file_exists($dest)) {
            throw new Error("File or directory '$dest' already exists.");

        } elseif (is_dir($source)) {
            static::createDir($dest);
            foreach (new \FilesystemIterator($dest) as $item) {
                static::delete($item);
            }
            foreach ($iterator = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($source, \RecursiveDirectoryIterator::SKIP_DOTS), \RecursiveIteratorIterator::SELF_FIRST) as $item) {
                if ($item->isDir()) {
                    static::createDir($dest . '/' . $iterator->getSubPathName());
                } else {
                    static::copy($item, $dest . '/' . $iterator->getSubPathName());
                }
            }

        } else {
            static::createDir(dirname($dest));
            if (@stream_copy_to_stream(fopen($source, 'r'), fopen($dest, 'w')) === FALSE) {
                throw new Error("Unable to copy file '$source' to '$dest'.");
            }
        }
    }


    /**
     * Deletes a file or directory.
     * @return void
     */
    public static function delete($path)
    {
        if (is_file($path) || is_link($path)) {
            $func = DIRECTORY_SEPARATOR === '\\' && is_dir($path) ? 'rmdir' : 'unlink';
            if (!@$func($path)) {
                throw new Error("Unable to delete '$path'.");
            }

        } elseif (is_dir($path)) {
            foreach (new \FilesystemIterator($path) as $item) {
                static::delete($item);
            }
            if (!@rmdir($path)) {
                throw new Error("Unable to delete directory '$path'.");
            }
        }
        clearstatcache();
    }


    /**
     * Renames a file or directory.
     * @return void
     */
    public static function rename($name, $newName, $overwrite = TRUE)
    {
        if (!$overwrite && file_exists($newName)) {
            throw new Error("File or directory '$newName' already exists.");

        } elseif (!file_exists($name)) {
            throw new Error("File or directory '$name' not found.");

        } else {
            static::createDir(dirname($newName));
            static::delete($newName);
            if (!@rename($name, $newName)) {
                throw new Error("Unable to rename file or directory '$name' to '$newName'.");
            }
        }
    }


    /**
     * Writes a string to a file.
     * @return bool
     */
    public static function write($file, $content, $mode = 0666, $flags = null)
    {
        static::createDir(dirname($file));
        if (@file_put_contents($file, $content, $flags) === FALSE) {
            throw new Error("Unable to write file '$file'.");
        }
        if ($mode !== NULL && !@chmod($file, $mode)) {
            throw new Error("Unable to chmod file '$file'.");
        }
    }


    /**
     * Is path absolute?
     * @return bool
     */
    public static function isAbsolute($path)
    {
        return (bool)preg_match('#[/\\\\]|[a-zA-Z]:[/\\\\]|[a-z][a-z0-9+.-]*://#Ai', $path);
    }

    /**
     * @param $url
     * @param $path
     * @param null $name
     */
    public static function download($url, $path, $name = null)
    {
        $read_file = fopen($url, "rb");
        if ($read_file) {
            static::createDir($path);
            $name = $name ? $name : basename($url);
            $write_file = fopen($path . DIRECTORY_SEPARATOR . $name, "wb");
            if ($write_file) {
                while (!feof($read_file)) {
                    fwrite($write_file, fread($read_file, 4096));
                }
                fclose($write_file);
            }
            fclose($read_file);
        }

    }

}
