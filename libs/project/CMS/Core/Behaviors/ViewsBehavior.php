<?php
namespace CMS\Core\Behaviors;

use CMS\Core\Helper\Helpers;
use Delorius\Behaviors\ORMBehavior;
use Delorius\Core\ORM;
use Delorius\Utils\Strings;

class ViewsBehavior extends ORMBehavior
{

    /** @var \Delorius\Http\Session
     * @service session
     * @inject
     */
    public $session;


    /**
     * @return ORM
     */
    public function updateViews()
    {
        $views = $this->session->getSection('_views');
        $table = $views->table;
        $orm = $this->getOwner();

        $tableId = Helpers::getTableId($orm);
        if (isset($table[$tableId][$orm->pk()])) {
            return $orm;
        } else {
            $table[$tableId][$orm->pk()] = time();
            $views->table = $table;
        }

        $orm->views = $orm->views + 1;
        return $orm;
    }

    /**
     * @param string $direction
     * @return ORM
     */
    public function sortViews($direction = 'desc')
    {
        $this->getOwner()->order_by($this->getOwner()->table_name().'.views', $direction);
        return $this->getOwner();
    }


    /**
     * @return string
     */
    public function getViews()
    {
        $views = $this->getOwner()->views;
        $str = Strings::pluralForm($views, _t('CMS:Core', 'views_1'), _t('CMS:Core', 'views_2'), _t('CMS:Core', 'views_5'));
        return _sf('{0} <span class="exp">{1}</span>', $views, $str);
    }

}