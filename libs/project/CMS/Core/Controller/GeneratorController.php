<?php

namespace CMS\Core\Controller;

use CMS\Core\Entity\Config\RobotsTxt;
use Delorius\Application\UI\Controller;
use Delorius\Utils\Finder;
use Delorius\Utils\Path;

class GeneratorController extends Controller
{

    /**
     * Генерация /robots.txt
     * @Get(false)
     */
    public function robotsAction()
    {
        $this->httpResponse->setContentType('text/plain', 'UTF-8');
        $host = $this->httpRequest->getUrl()->getHost();
        if ($this->httpRequest->getUrl()->getScheme() === 'https') {
            $host = 'https://' . $host;
        }
        $text = "User-Agent: *\nAllow: /\nDisallow: */?*sort*\nHost: $host";
        $robots = RobotsTxt::model()->cached()->find_all();
        $domain = getHostParameter('_route');
        foreach ($robots as $item) {
            if ($item->domain == $domain) {
                if ($item->value) {
                    $text = $item->value;
                }
                break;
            }
        }

        $dir = $this->container->getParameters('path.sitemaps');
        if (file_exists($dir)) {
            /** @var \Delorius\Routing\DomainRouter $domainRouter */
            $domainRouter = $this->container->getService('domainRouter');
            $host = $domainRouter->generate($domain);
            foreach (Finder::findFiles($domain . '_*.xml')->in($dir) as $pathFile) {
                $text .= "\nSitemap: " . $host . Path::localPath(DIR_INDEX, $pathFile->getPathname());

            }
        }
        echo $text;
        die;
    }


}