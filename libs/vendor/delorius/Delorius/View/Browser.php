<?php
namespace Delorius\View;

use Delorius\Http\IRequest;
use Delorius\Http\IResponse;

class Browser
{

    const NAME_NO_MOBILE = 'df_no_mobile';
    /**
     * @var \Browser
     */
    protected $browser;

    /**
     * @var IRequest
     */
    protected $httpRequest;

    /**
     * @var IResponse
     */
    protected $httpResponse;


    public function __construct(IResponse $httpResponse, IRequest $httpRequest)
    {
        $this->httpResponse = $httpResponse;
        $this->httpRequest = $httpRequest;
        $this->browser = new \Browser();
    }

    public function isMobile()
    {
        return $this->browser->isMobile();
    }

    public function isDesktop()
    {
        return $this->browser->isDesktop();
    }

    public function isTable()
    {
        return $this->browser->isTablet();
    }

    /**
     * Set required desktop version
     */
    public function setFullVersion()
    {
        $this->httpResponse->setCookie(self::NAME_NO_MOBILE, true, '+1 days');
    }

    /**
     * Set required desktop version
     */
    public function cancelFullVersion()
    {
        $this->httpResponse->setCookie(self::NAME_NO_MOBILE, null, 0);
    }

    /**
     * Check required desktop version
     * @return bool
     */
    public function isFullVersion()
    {
        $query = $this->httpRequest->getRequest(self::NAME_NO_MOBILE, false);
        $cookie = $this->httpRequest->getCookie(self::NAME_NO_MOBILE, false);

        if ($query || $cookie) {
            if (!$cookie)
                $this->setFullVersion();
            return true;
        }

        $this->cancelFullVersion();
        return false;
    }

}