<?php
namespace CMS\Banners\Controller;

use CMS\Banners\Entity\Banner;
use Delorius\Application\UI\Controller;

class RedirectController extends Controller{

    /**
     * @Model(name=CMS\Banners\Entity\Banner)
     */
    public function goAction(Banner $model){
        $model->click++;
        $model->save();
        $this->httpResponse->redirect($model->url,false);
    }

}