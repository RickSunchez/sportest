<?php
namespace CMS\Core\Controller;

use CMS\Core\Entity\Page;
use Delorius\Application\UI\Controller;
use Delorius\Utils\Strings;
use Delorius\View\Browser;

class HomeController extends Controller
{

    /**
     * @service site
     * @inject
     */
    public $site;

    /**
     * @var Browser
     * @service browser
     * @inject
     */
    public $browser;


    /**
     * Показ главной страницы
     */
    public function indexAction()
    {
        $theme = $var = null;
        $page = Page::model()->cached()->main()->site(getHostParameter('_route'))->find();
        if ($page->loaded()) {

            if ($this->site->mobile && $this->browser->isMobile() && !$this->browser->isFullVersion()) {
                $this->site->mobile = $page->mobile ? $page->mobile : $this->site->mobile;
                $this->template($this->site->mobile);
            } else {
                $this->template($page->template_dir);
            }

            $this->layout($page->template_page);
            /** @var \CMS\Core\Component\Header\HeaderControl $header */
            $header = $this->getHeader();
            if ($page->keys) {
                $header->addKeywords($page->keys);
            }

            $property_og = $page->getOptions('og');
            $property = array();
            foreach ($property_og as $opt) {
                if ($opt->value) {
                    $property[$opt->code . ':' . $opt->name] = Strings::escape($opt->value);
                }
            }

            $var['image'] = $image = $page->getImage();
            if (!isset($property['og:image']) && $image->loaded()) {
                $property['og:image'] = $image->normal;
            }

            if (!$property['og:title']) {
                $property['og:title'] = $page->title;
            }

            if (!$property['og:description']) {
                $property['og:description'] = $page->description;
            }

            #og
            $this->setMeta(null, array(
                'desc' => $page->description,
                'property' => $property
            ));

            $this->getHeader()->setTitle($page->title);

            $var['page'] = $page;
            $theme = $page->prefix ? '_' . $page->prefix : '';
            $this->lastModified($page->date_edit ? $page->date_edit : $page->date_cr);
            $this->setGUID($page->pk());
        }
        $this->response($this->view->load('cms/page/home' . $theme, $var));
    }

}