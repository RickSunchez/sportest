<?php
namespace CMS\Admin\Controller;

use CMS\Admin\Entity\Admin;
use CMS\Core\Component\Register;
use CMS\Core\Entity\Callback;
use Delorius\Application\UI\Controller;
use Delorius\Exception\NotFound;
use Delorius\Exception\OrmValidationError;
use Delorius\Page\Pagination\PaginationBuilder;
use Delorius\Utils\Arrays;

/**
 * @Template(name=admin)
 * @Admin
 * @SetTitle Заявки на обратный звонок #admin_callback?action=list
 *
 */
class CallbackController extends Controller
{

    /**
     * @var Register
     * @inject
     */
    public $register;

    /**
     * @AddTitle Список
     */
    public function listAction($page)
    {
        $callback = Callback::model()->order_created('DESC');
        $get = $this->httpRequest->getQuery();

        $pagination = PaginationBuilder::factory($callback)
            ->setItemCount(false)
            ->setPage($page)
            ->setItemsPerPage(20)
            ->addQueries($get)
            ->setRoute('admin_callback');

        $result = $pagination->result();
        $var['pagination'] = $pagination;
        $var['callback'] = Arrays::resultAsArray($result);
        $var['users'] = Arrays::resultAsArray(Admin::model()->cached()->find_all());
        $this->response($this->view->load('cms/callback/list', $var));
    }


    public function deleteDataAction()
    {
        $id = (int)$this->httpRequest->getPost('id');
        $callback = new Callback($id);
        if ($callback->loaded()) {
            $register = $this->register;
            $callback->onAfterDelete[] = function ($orm) use ($register) {
                $register->add(
                    Register::TYPE_INFO,
                    Register::SPACE_ADMIN,
                    'Заявка удалена: "[subject]" ',
                    $orm
                );
            };
            $callback->delete(true);
        }
        $this->response(array('ok'));
    }


    public function treatDataAction()
    {
        $post = $this->httpRequest->getPost();
        try {
            $callback = new Callback($post['id']);
            if ($callback->loaded()) {
                $callback->user_id = $this->user->getId();
                $callback->date_finished = time();

                $register = $this->register;
                $callback->onAfterSave[] = function ($orm) use ($register) {
                    $register->add(
                        Register::TYPE_INFO,
                        Register::SPACE_ADMIN,
                        'Заявка отмечена как прочитанная: "[subject]" ',
                        $orm
                    );
                };

                $callback->save(true);

                $result['ok'] = 'Готово';
                $result['callback'] = $callback->as_array();
            } else {
                $result['error'] = _t('CMS:Admin', 'Object not found');
            }
        } catch (OrmValidationError $e) {
            $result['error'] = _t('CMS:Admin', 'Error');
        }
        $this->response($result);
    }


}