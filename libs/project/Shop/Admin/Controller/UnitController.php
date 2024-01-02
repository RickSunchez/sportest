<?php
namespace Shop\Admin\Controller;

use Delorius\Application\UI\Controller;
use Delorius\Exception\OrmValidationError;
use Delorius\Utils\Arrays;
use Shop\Commodity\Entity\Unit;

/**
 * @Template(name=admin)
 * @Admin
 * @SetTitle Ед. Измерения #admin_unit?action=list
 */
class UnitController extends Controller
{

    /**
     * @AddTitle Список
     */
    public function listAction()
    {
        $units = Unit::model()->select()->cached()->sort()->find_all();
        $var['units'] = Arrays::resultAsArray($units, false);
        $this->response($this->view->load('shop/goods/unit/list', $var));
    }

    /**
     * @Post
     */
    public function saveDataAction()
    {
        $post = $this->httpRequest->getPost();
        try {
            $unit = new Unit($post['unit_id']);
            $unit->values($post);
            $unit->save(true);

            $result['ok'] = _t('CMS:Admin', 'These modified');
        } catch (OrmValidationError $e) {
            $result['errors'] = $e->getErrorsMessage();
        }
        $units = Unit::model()->select()->cached()->sort()->find_all();
        $result['units'] = Arrays::resultAsArray($units, false);
        $this->response($result);
    }

    /**
     * @Post
     */
    public function deleteDataAction()
    {
        $post = $this->httpRequest->getPost();
        $unit = new Unit($post['unit_id']);
        if ($unit->loaded()) {
            $unit->delete(true);
        }
        $this->response(array('ok'));
    }


}