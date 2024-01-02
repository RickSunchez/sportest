<?php
namespace CMS\Admin\Controller;

use CMS\Core\Entity\File;
use Delorius\Application\UI\Controller;
use Delorius\Exception\OrmValidationError;

class FileController extends Controller
{

    /**
     * @Post
     */
    public function saveDataAction()
    {
        $post = $this->httpRequest->getPost();
        $File = new File($post[File::model()->primary_key()]);
        if ($File->loaded()) {
            try {
                $File->values($post);
                $File->save(true);
                $result['ok'] = _t('CMS:Admin', 'These modified');
                $result['File'] = $File->as_array();
            } catch (OrmValidationError $e) {
                $result['errors'] = $e->getErrorsMessage();
            }
        } else {
            $result['errors'][] = _t('CMS:Admin', 'Object not found');
        }
        $this->response($result);
    }

    /**
     * @Post
     * @Model(name=CMS\Core\Entity\File)
     */
    public function deleteDataAction(File $model)
    {
        $model->delete(true);
        $this->response(array('ok' => _t('CMS:Admin', 'Object is deleted')));
    }

    /**
     * @Post
     */
    public function addDataAction()
    {
        $result = array('error' => _t('CMS:Admin', 'Could not load file'));
        $id = $this->httpRequest->getPost('id');
        $class = $this->httpRequest->getPost('class');
        $object = new $class($id);
        if ($object->loaded()) {
            $file = $this->httpRequest->getFile('file');
            $res = $object->addFile($file);
            if ($res) {
                $result = $res;
            }
        }
        $this->response($result);
    }

    /**
     * @Post
     */
    public function setDataAction()
    {
        $result = array('error' => _t('CMS:Admin', 'Could not load file'));
        $id = $this->httpRequest->getPost('id');
        $class = $this->httpRequest->getPost('class');
        $object = new $class($id);
        if ($object->loaded()) {
            $file = $this->httpRequest->getFile('file');
            $res = $object->setFile($file);
            if ($res) {
                $result = $res;
            }
        }
        $this->response($result);
    }


}