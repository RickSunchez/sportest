<?php
namespace Boat\Core\Controller;


class CallbackController extends \CMS\Core\Controller\CallbackController
{

    /**
     * @Post
     */
    public function realtimeAction()
    {

        if (is_work()) {
            $form = $this->httpRequest->getRequest('form');

            $phone = str_replace('+7', '8', $form['phone']);
            $phone = str_replace(array(' ', '-', '(', ')'), '', $phone);

            if (!$phone) {
                $this->response(array('error' => 'Укажите телефон'));
                return;
            }

            $strHost = "trade.sportest.ru";
            $strUser = "amiuser";
            $strSecret = "j,hfnysqdspjd";

            $ringgroupnumber = '626';
            $strChannel = 'Local/' . $ringgroupnumber . '@ringgroups';
            $strContext = "from-webcall1";
            $strWaitTime = "60";
            $strPriority = "1";
            $strExten = $phone;
            $strCallerId = "webcall:$strExten"; //set in pbx in different trunks Outbound CID

            if (true) {
                $oSocket = fsockopen($strHost, 5038, $errnum, $errdesc) or die("Connection to host failed");
                fputs($oSocket, "Action: login\r\n");
                fputs($oSocket, "Events: off\r\n");
                fputs($oSocket, "Username: $strUser\r\n");
                fputs($oSocket, "Secret: $strSecret\r\n\r\n");
                fputs($oSocket, "Action: originate\r\n");
                fputs($oSocket, "Channel: $strChannel\r\n");
                fputs($oSocket, "WaitTime: $strWaitTime\r\n");
                fputs($oSocket, "CallerId: $strCallerId\r\n");
                fputs($oSocket, "Exten: $strExten\r\n");
                fputs($oSocket, "Context: $strContext\r\n");
                fputs($oSocket, "Priority: $strPriority\r\n\r\n");
                fputs($oSocket, "Action: Logoff\r\n\r\n");
                sleep(1);
                fclose($oSocket);
            }

            $this->container->getService('logger')->info(var_export($form,true), 'callback');

            $result['ok'] = _t('CMS:Core', 'Message is sent');
            $result['realtime'] = true;
            $this->response($result);

        } else {
            $this->sendAction();
        }


    }

}