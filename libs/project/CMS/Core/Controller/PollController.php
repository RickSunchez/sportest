<?php
namespace CMS\Core\Controller;

use CMS\Core\Entity\Poll;
use Delorius\Application\UI\Controller;
use Delorius\Utils\Arrays;

class PollController extends Controller
{

    public function displayPartial($id = null)
    {
        if ($id) {
            $poll = new Poll($id);
        } else {
            $poll = Poll::model()->sort()->active()->find();
        }

        if (!$poll->loaded()) {
            return null;
        }

        $var['poll'] = $poll;
        $var['items'] = $poll->getItems();
        $var['is_poll'] = $this->httpRequest->getCookie('poll_' . $poll->pk(), false);
        $this->response($this->view->load('cms/poll/_display', $var));
    }

    /**
     * @Post
     * @Model(name=CMS\Core\Entity\Poll)
     */
    public function voteDataAction(Poll $model)
    {
        $result = array();
        $is_poll = $this->httpRequest->getCookie('poll_' . $model->pk(), false);
        if (!$is_poll) {
            $item_id = $this->httpRequest->getPost('item_id', null);
            $res = $model->vote($item_id);

            if ($res) {
                $this->httpResponse->setCookie('poll_' . $model->pk(),true,time()+60*60*24*60);
                $result['ok'] = 'Ваш голос принят';
                $result['poll'] = $model->as_array();
                $result['items'] = Arrays::resultAsArray($model->getItems());
            } else {
                $result['error'] = 'Не удалось проголосовать';
            }
        } else {
            $result['error'] = 'Вы уже голосовали';
        }
        $this->response($result);

    }

}