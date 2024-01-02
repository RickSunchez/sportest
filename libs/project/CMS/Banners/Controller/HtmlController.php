<?php
namespace CMS\Banners\Controller;

use CMS\Banners\Entity\Banner;
use Delorius\Application\UI\Controller;
use Delorius\DataBase\DB;

class HtmlController extends Controller
{

    public function listPartial($code, $limit = 1, $width = null, $theme = null, $rnd = false)
    {
        $banners = Banner::model()
            ->whereByCode($code)
            ->active();

        if ($limit) {
            $banners->limit($limit);
        }

        if ($rnd) {
            $banners->order_by(DB::expr('RAND()'));
        } else {
            $banners->sort()->cached();
        }

        $time = strtotime('- 1 day');

        $arr = array();
        $ids = array();
        $ids_up = array();
        $result = $banners->find_all();
        foreach ($result as $banner) {
            if ($banner->date_show_up == 0) {
                $ids[] = $banner->pk();
                $arr[] = $banner;
                $banner->visit++;
                $banner->save();
            } elseif ($banner->date_show_up >= $time) {
                $ids[] = $banner->pk();
                $arr[] = $banner;
                $banner->visit++;
                $banner->save();
            } else {
                $ids_up[] = $banner->pk();
            }
        }

        #off banner
        if (sizeof($ids_up)) {
            $obj_banner = Banner::model();
            DB::update($obj_banner->table_name())
                ->set(array('status' => 0, 'date_show_up' => 0))
                ->where($obj_banner->primary_key(), 'in', $ids_up)
                ->execute($obj_banner->db_config());
            $obj_banner->cache_delete();
        }
        #end off banner


        $var['banners'] = $arr;
        $var['width'] = $width;
        $var['code'] = $code;
        $theme = $theme ? '_' . $theme : '';
        $this->response($this->view->load('cms/banner/list' . $theme, $var));

    }

}