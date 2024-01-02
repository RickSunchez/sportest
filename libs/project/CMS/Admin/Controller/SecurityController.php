<?php
namespace CMS\Admin\Controller;

use CMS\Users\Entity\ACL;
use CMS\Users\Entity\Role;
use Delorius\Application\UI\Controller;
use Delorius\Exception\NotFound;
use Delorius\Exception\OrmValidationError;
use Delorius\Utils\Arrays;

/**
 * @Template(name=admin)
 * @Admin
 */
class SecurityController extends Controller
{
    /**
     * @var \Delorius\Page\Breadcrumb\BreadcrumbBuilder
     * @service breadCrumbs
     * @inject
     */
    public $breadCrumbs;

    /**
     * @var \Delorius\Migration\MigrationManager
     * @service migrationManager
     * @inject
     */
    public $migration;

    public function before()
    {
        $type = $this->httpRequest->getQuery('type') ?
            $this->httpRequest->getQuery('type') :
            $this->httpRequest->getPost('type');

        if ($type == null) {
            throw new NotFound('Не указан тип');
        }
        $this->breadCrumbs->addLink(
            'Права доступа',
            link_to('admin_acl', array('action' => 'roles', 'type' => $type)),
            null,
            false
        );
    }


    /**
     * @param $type
     * @throws \Delorius\Exception\NotFound
     * @AddTitle Список
     */
    public function rolesAction($type)
    {
        $roles = Role::model()->where('type', '=', $type)->find_all();
        $var['roles'] = Arrays::resultAsArray($roles);
        $var['type'] = $type;
        $this->response($this->view->load('cms/acl/roles', $var));
    }

    /**
     * @param $type
     * @AddTitle ORM
     */
    public function ormAction($type, $role)
    {
        $items = $this->migration->getItems();
        $var['orms'] = array();
        foreach ($items as $item) {
            $arr = array();
            $arr['object_name'] = str_replace('\\', ':', $item->getModel()->object_name());
            $arr['table_name'] = $item->getModel()->table_name();
            $var['orms'][] = $arr;
        }

        $acl = ACL::model()
            ->where('type', '=', $type)
            ->where('target_type', '=', 'role')
            ->where('target_id', '=', $role)
            ->find_all();

        $var['acl'] = Arrays::resultAsArray($acl);
        $var['code'] = $role;
        $var['type'] = $type;
        $this->response($this->view->load('cms/acl/orm', $var));
    }

    /**
     * @Post
     */
    public function ormDataAction()
    {
        $post = $this->httpRequest->getPost();
        try {
            $code = $post['code'];
            $resource = $post['orm']['table_name'];
            $action = $post['action'];
            $type = $post['type'];
            $status = $post['orm']['action'][$action];

            $acl = ACL::model()
                ->where('target_type', '=', 'role')
                ->where('target_id', '=', $code)
                ->where('resource', '=', $resource)
                ->where('privilege', '=', $action)
                ->where('type', '=', $type)
                ->find();

            if ($acl->loaded() && $status == '-1') {
                $acl->delete(true);
            } else {
                $acl->target_type = 'role';
                $acl->target_id = $code;
                $acl->type = $type;
                $acl->resource = $resource;
                $acl->privilege = $action;
                $acl->status = $status;
                $acl->save(true);
            }
            $result['ok'] = 1;
        } catch (OrmValidationError $e) {
            $result['errors'] = $e->getErrorsMessage();
        }
        $this->response($result);
    }

}