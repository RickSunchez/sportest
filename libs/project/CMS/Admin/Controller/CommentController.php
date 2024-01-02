<?php
namespace CMS\Admin\Controller;

use CMS\Core\Entity\Comment;
use Delorius\Application\UI\Controller;

class CommentController extends Controller
{
    /**
     * @Post
     */
    public function saveDataAction()
    {
        $post = $this->httpRequest->getPost();
        $class = $post['class'];
        $id = $post['id'];
        $comment = $post['comment'];
        $orm = new $class($id);
        if ($orm->loaded()) {
            $res = $orm->addComment($comment);
            if ($res) {
                $result = $res;
                $result['ok'] = _t('CMS:Admin', 'These modified');
            } else {
                $result['ok'] = _t('CMS:Admin', 'Object is deleted');
            }
        } else {
            $result['error'] = _t('CMS:Admin', 'Object not found');
        }

        $this->response($result);
    }

    /**
     * @Admin
     * @Post
     * @Model(name=CMS\Core\Entity\Comment)
     */
    public function deleteDataAction(Comment $model)
    {
        $model->delete(true);
        $this->response(array('ok' => _t('CMS:Admin', 'Object is deleted')));
    }


}