<?php
namespace CMS\Core\Controller;

use CMS\Core\Component\Register;
use CMS\Core\Entity\Callback;
use CMS\Mail\Model\Notification\NotifySystem;
use Delorius\Application\UI\Controller;
use Delorius\Exception\OrmValidationError;
use Delorius\Http\FileUpload;
use Delorius\Http\Url;

class CallbackController extends Controller
{

    /**
     * @var NotifySystem
     * @service notify.system
     * @inject
     */
    public $notify;

    /**
     * @var Register
     * @inject
     */
    public $register;

    /**
     * @Post
     */
    public function sendAction()
    {
        $post = $this->httpRequest->getPost();
        if (count($post['form']) == 0) {
            die();
        }
        try {
            $subject = $post['form']['subject'];
            unset($post['form']['subject']);
            $callback = new Callback();
            $callback->subject = $subject;
            $callback->setConfig($post);
            $callback->save(true);
            $msg = '<p>' . $subject . '</p>';
            foreach ($post['form'] as $name => $value) {
                $msg .= '<p>' . _t('CMS:Core', $name) . ' : ' . $value . '</p>';
            }
            $msg .= '<br />' . $callback->renderLink();
            $files = $this->httpRequest->getFiles();
            if (count($files['file'])) {
                /** @var FileUpload $file */
                foreach ($files['file'] as $file) {
                    if ($file instanceof FileUpload)
                        $this->notify->addFile($file->getTemporaryFile(), $file->getName());
                }
            }
            $this->notify->send($subject, $msg);
            $result['ok'] = _t('CMS:Core', 'Message is sent');

            if (isset($post['return'])) {
                $this->setFlash('ok', $result['ok']);
                $this->backReturn();
            }

        } catch (OrmValidationError $e) {
            $result['errors'] = $e->getErrorsMessage();

            if (isset($post['return'])) {
                $this->setFlash('errors', $result['errors']);
                $this->backReturn($post['return']);
            }
        }
        $this->response($result);
    }


    protected function backReturn($url = null)
    {
        if ($url == null) {
            $url = new Url($_SERVER['HTTP_REFERER']);
        }

        $url = new Url($url);
        $this->httpResponse->redirect($url);

    }

    /**
     * @param $hash
     * @Model(name=CMS\Core\Entity\Callback)
     * @Admin(isLoggedIn=false)
     */
    public function activeAction(Callback $model, $hash)
    {
        if ($model->code == $hash) {
            $model->date_finished = time();

            $register = $this->register;
            $model->onAfterSave[] = function ($orm) use ($register) {
                $register->add(
                    Register::TYPE_INFO,
                    Register::SPACE_SITE,
                    'Заявка отмечена как прочитанная через почту: "[subject]" ',
                    $orm
                );
            };

            $model->save();
        }

        $this->httpResponse->redirect(link_to('homepage'));
    }
}