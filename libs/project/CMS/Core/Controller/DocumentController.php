<?php
namespace CMS\Core\Controller;

use CMS\Core\Entity\Document;
use CMS\Core\Entity\File;
use Delorius\Application\UI\Controller;
use Delorius\Exception\ForbiddenAccess;
use Delorius\Utils\MimeTypeDetector;

class DocumentController extends Controller
{

    /**
     * @Model(name=CMS\Core\Entity\Document)
     */
    public function downloadAction(Document $model, $hash)
    {

        if ($hash != $model->code) {
            throw new ForbiddenAccess();
        }
        $model->count++;
        $model->save(true);

        $contentDisposition = 'attachment;';
        if ($this->isInlineByExt($model->ext)) {
            $contentDisposition = 'inline; ';
        }

        $path = DIR_INDEX . $model->path;
        $file_size = filesize($path);
        $file_time = filemtime($path);
        $file_date = gmdate('D, d M Y H:i:s T', $file_time);
        $ext = strtolower(substr(strrchr($model->path, '.'), 1));
        $contentType = MimeTypeDetector::getContentType($ext);
        $name = substr(basename($model->path), 11);

        $this->httpResponse->setHeader('Content-Type', $contentType);
        $this->httpResponse->setHeader('Content-Length', $file_size);
        $this->httpResponse->setHeader('Accept-Ranges', 'bytes');
        $this->httpResponse->setHeader('Content-Disposition', $contentDisposition . '  filename="' . $name . '"');
        $this->httpResponse->setHeader('Expires', '0');
        $this->httpResponse->setHeader('Last-Modified', $file_date);
        $this->httpResponse->setHeader('Cache-Control', 'must-revalidate');
        $this->httpResponse->setHeader('Pragma', 'public');
        readfile($path);
        exit;
    }

    /**
     * @Model(name=CMS\Core\Entity\File)
     */
    public function downloadFileAction(File $model)
    {
        $model->count++;
        $model->save(true);

        $contentDisposition = 'attachment;';
        if ($this->isInlineByExt($model->ext)) {
            $contentDisposition = 'inline; ';
        }

        $path = DIR_INDEX . $model->path;
        $file_size = filesize($path);
        $file_time = filemtime($path);
        $file_date = gmdate('D, d M Y H:i:s T', $file_time);
        $ext = strtolower(substr(strrchr($model->path, '.'), 1));
        $contentType = MimeTypeDetector::getContentType($ext);
        $name = substr(basename($model->path), 11);

        $this->httpResponse->setHeader('Content-Type', $contentType);
        $this->httpResponse->setHeader('Content-Length', $file_size);
        $this->httpResponse->setHeader('Accept-Ranges', 'bytes');
        $this->httpResponse->setHeader('Content-Disposition', $contentDisposition . '  filename="' . $name . '"');
        $this->httpResponse->setHeader('Expires', '0');
        $this->httpResponse->setHeader('Last-Modified', $file_date);
        $this->httpResponse->setHeader('Cache-Control', 'must-revalidate');
        $this->httpResponse->setHeader('Pragma', 'public');
        readfile($path);
        exit;
    }

    protected function isInlineByExt($ext)
    {
        if ($ext == 'pdf') {
            return true;
        }

        if ($ext == 'jpg' || $ext == 'jpeg' || $ext == 'png') {
            return true;
        }

        return false;
    }


}