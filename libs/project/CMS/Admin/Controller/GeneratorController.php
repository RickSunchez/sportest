<?php
namespace CMS\Admin\Controller;

use CMS\Core\Entity\Config\Analytics;
use CMS\Core\Entity\Config\RobotsTxt;
use Delorius\Application\UI\Controller;
use Delorius\Exception\OrmValidationError;
use Delorius\Utils\Arrays;

/**
 * @Template(name=admin)
 * @Admin
 */
class GeneratorController extends Controller{

    /**
     * @AddTitle Генератор robots.txt
     */
    public function listRobotsAction(){
        $domain = $this->container->getParameters('domain');
        $var['domain'] = array();
        foreach($domain as $name=>$config){
            $var['domain'][] = array(
                'name'=>$name,
                'host'=>$config[0],
            );
        }
        $robots = RobotsTxt::model()->find_all();
        $var['robots'] = Arrays::resultAsArray($robots);
        $this->response($this->view->load('cms/robots/list',$var));
    }

    /**
     * @Post
     */
    public function saveRobotsDataAction(){
        $post = $this->httpRequest->getPost();

        try{
            $robots = new RobotsTxt($post['robots_id']);
            $robots->values($post);
            $robots->save(true);

            $result['ok'] = _t('CMS:Admin','These modified');
            $result['robots'] = $robots->as_array();
        }catch (OrmValidationError $e){
            $result['errors'] = $e->getErrorsMessage();
        }
        $this->response($result);
    }

    /**
     * @AddTitle Генератор код аналитики
     */
    public function listAnalyticsAction(){
        $domain = $this->container->getParameters('domain');
        $var['domain'] = array();
        foreach($domain as $name=>$config){
            $var['domain'][] = array(
                'name'=>$name,
                'host'=>$config[0],
            );
        }
        $analytics = Analytics::model()->find_all();
        $var['analytics'] = Arrays::resultAsArray($analytics);
        $this->response($this->view->load('cms/analytics/list',$var));
    }

    /**
     * @Post
     */
    public function saveAnalyticsDataAction(){
        $post = $this->httpRequest->getPost();
        try{
            $analytics = new Analytics($post['id']);
            $analytics->values($post);
            $analytics->save(true);
            $result['ok'] = _t('CMS:Admin','These modified');
            $result['analytics'] = $analytics->as_array();
        }catch (OrmValidationError $e){
            $result['errors'] = $e->getErrorsMessage();
        }
        $this->response($result);
    }


}