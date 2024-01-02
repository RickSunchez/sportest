<?php
namespace CMS\Admin\Controller;

use CMS\Core\Entity\FileIndex;
use Delorius\Application\UI\Controller;
use Delorius\Exception\OrmValidationError;
use Delorius\Page\Pagination\PaginationBuilder;
use Delorius\Utils\Arrays;

/**
 * @Template(name=admin)
 * @Admin
 * @SetTitle Индексовые файлы
 *
 */
class FileIndexController extends Controller
{
    public function listAction()
    {
        $files = FIleIndex::model()->order_created('desc')->find_all();
        $var['files'] = Arrays::resultAsArray($files);
        $this->response($this->view->load('cms/file_index/list', $var));
    }

    /**
     * @throws \Delorius\Exception\Error
     * @Post
     */
    public function deleteDataAction()
    {
        $post = $this->httpRequest->getPost();
        try {
            $file = new FileIndex($post['file_id']);
            if ($file->loaded()) {
                $file->delete(true);
            }
            $result['ok'] = _t('CMS:Admin', 'Ready');
        } catch (OrmValidationError $e) {
            $result['error'] = $e->getErrorsMessage();
        }
        $this->response($result);
    }

    /**
     * @Post
     */
    public function uploadDataAction()
    {
        $result = array('error' => _t('CMS:Admin', 'Could not load file'));
        try {
            $file = new FileIndex();
            $res = $file->setFile($this->httpRequest->getFile('file'));
            if ($res) {
                $result = $file->as_array();
            }
        } catch (OrmValidationError $e) {
            $result = array('error' => _t('CMS:Admin', 'Could not load file'));
        }
        $this->response($result);
    }
}