<?php
namespace Delorius\Http;

use Delorius\Core\Object;
use Delorius\Core\Image;
use Delorius\Exception\Error;
use Delorius\Utils\Strings;
use Delorius\Utils\MimeTypeDetector;
use Delorius\Utils\Dir;


class FileUpload extends Object
{
    /** @var string */
    private $name;

    /** @var string */
    private $type;

    /** @var string */
    private $size;

    /** @var string */
    private $tmpName;

    /** @var int */
    private $error;


    public function __construct($value)
    {

        foreach (array('name', 'size', 'tmp_name', 'error') as $key) {
            if (!isset($value[$key]) || !is_scalar($value[$key])) {
                $this->error = UPLOAD_ERR_NO_FILE;
                return;
            }
        }

        $this->name = trim(Strings::webalize(Strings::translit($value['name']),'.#_'));
        $this->size = $value['size'];
        $this->tmpName = $value['tmp_name'];
        $this->error = $value['error'];
        $this->type = $this->getContentType();
    }


    /**
     * Returns the file name.
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    public function getType()
    {
        return $this->type;
    }


    /**
     * Returns the sanitized file name.
     * @return string
     */
    public function getSanitizedName()
    {
        return $this->name;
    }


    /**
     * Returns the MIME content type of an uploaded file.
     * @return string
     */
    public function getContentType()
    {
        if ($this->isOk() && $this->type === NULL) {
            $this->type = MimeTypeDetector::fromFile($this->tmpName);
        }
        return $this->type;
    }


    /**
     * Returns the size of an uploaded file.
     * @return int
     */
    public function getSize()
    {
        return $this->size;
    }


    /**
     * Returns the path to an uploaded file.
     * @return string
     */
    public function getTemporaryFile()
    {
        return $this->tmpName;
    }


    /**
     * Returns the path to an uploaded file.
     * @return string
     */
    public function __toString()
    {
        return $this->tmpName;
    }


    /**
     * Returns the error code. {@link http://php.net/manual/en/features.file-upload.errors.php}
     * @return int
     */
    public function getError()
    {
        return $this->error;
    }


    /**
     * Is there any error?
     * @return bool
     */
    public function isOk()
    {
        return $this->error === UPLOAD_ERR_OK;
    }


    /**
     * Move uploaded file to new location.
     * @param  string
     * @return FileUpload  provides a fluent interface
     */
    public function move($dest, $name){
        if (!call_user_func(is_uploaded_file($this->tmpName) ? 'move_uploaded_file' : 'rename', $this->tmpName, $dest . $name)) {
            throw new Error ("Unable to move uploaded file '$this->tmpName' to '$dest'.");
        }
        $this->tmpName = $dest . $name;
        return $this;
    }


    /**
     * Is uploaded file GIF, PNG or JPEG?
     * @return bool
     */
    public function isImage()
    {
        return in_array($this->getContentType(), array('image/gif', 'image/png', 'image/jpeg'), TRUE);
    }


    /**
     * Returns the image.
     * @return \Delorius\Core\Image
     */
    public function toImage()
    {
        return Image::fromFile($this->tmpName);
    }


    /**
     * Returns the dimensions of an uploaded image as array.
     * @return array
     */
    public function getImageSize()
    {
        return $this->isOk() ? @getimagesize($this->tmpName) : NULL; // @ - files smaller than 12 bytes causes read error
    }


    /**
     * Get file contents.
     * @return string
     */
    public function getContents()
    {
        // future implementation can try to work around safe_mode and open_basedir limitations
        return $this->isOk() ? file_get_contents($this->tmpName) : NULL;
    }

}



