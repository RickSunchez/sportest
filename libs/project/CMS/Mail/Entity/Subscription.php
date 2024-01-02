<?php
namespace CMS\Mail\Entity;

use CMS\Go\Model\GoCookieHelper;
use CMS\Mail\Model\SubscriberBuilder;
use Delorius\Core\ORM;
use Delorius\Exception\Error;
use Delorius\Exception\OrmValidationError;
use Delorius\Utils\Strings;
use Delorius\Utils\Validators;

class Subscription extends ORM
{
    const TYPE_BID = 'bid';
    const TYPE_SUB = 'sub';

    public static function hasHash($hash)
    {
        return self::model()->where('hash', '=', $hash)->find()->loaded();
    }


    public function getTypes()
    {
        return array(
            self::TYPE_BID => 'Заявка',
            self::TYPE_SUB => 'Подписка',
        );
    }

    public function getTypeName()
    {
        $type = $this->getTypes();
        return $type[$this->type];
    }

    public function as_array()
    {
        $arr = parent::as_array();
        $arr['type_name'] = $this->getTypeName();
        return $arr;
    }

    /**
     * Принимает заявку
     * @param array $request ('name','email','phone','comment')
     * @return bool
     */
    public function addBid(array $request)
    {
        if (!$this->loaded())
            return false;

        switch ($this->type) {
            case self::TYPE_BID:
                try {
                    $bid = new SubscriptionBid();
                    $bid->name = $request['name'];
                    $bid->email = $request['email'];
                    $bid->phone = $request['phone'];
                    $bid->comment = $request['comment'];
                    $bid->group_id = $this->pk();
                    list($goId, $isMail) = GoCookieHelper::GetCookie();
                    $bid->go_id = $goId;
                    $bid->is_mail = $isMail;
                    $bid->note = '';
                    $bid->save(true);
                } catch (OrmValidationError $e) {
                    $this->error($e->getErrorsMessage(), 'BidSubscriber');
                    return false;
                }
            case self::TYPE_SUB:
                $isSubscription = false;
                try {
                    if (Validators::isEmail($request['email'])) {
                        $builder = new SubscriberBuilder($request);
                        $sub = $builder->getOwner();
                        if ($sub->loaded()) {
                            $isSubscription = $this->subscribe($sub);
                        }
                    }
                } catch (Error $e) {
                    $this->error($e->getMessage(), 'SubscribeClient');
                    return false;
                }
                break;
        }

        if (!$isSubscription) {
            $this->count++;
            $this->save(true);
        }
        return true;
    }

    /**
     * Отписать пользователя от подписки
     * @return bool
     */
    public function unsubscribe(Subscriber $subscriber)
    {
        if (!$this->loaded())
            return false;

        $groupSubscriber = SubscriberGroup::model()
            ->where('sub_id', '=', $subscriber->pk())
            ->where('group_id', '=', $this->pk())
            ->find();
        if ($groupSubscriber->loaded()) {
            $groupSubscriber->delete(true);
            if ($this->type == self::TYPE_SUB) {
                $this->count--;
                $this->save(true);
                $this->warning('Подписчик ' . $subscriber->email . ' отписался от группы подписки groupId = ' . $this->pk(), 'Subscription');
            }
            return true;
        }
        return false;
    }

    /**
     * Подписать пользователя к подписки
     * @return bool
     */
    public function subscribe(Subscriber $subscriber)
    {
        if (!$this->loaded())
            return false;

        $groupSubscriber = SubscriberGroup::model()
            ->where('group_id', '=', $this->pk())
            ->where('sub_id', '=', $subscriber->pk())
            ->find();

        if (!$groupSubscriber->loaded()) {
            $groupSubscriber->group_id = $this->pk();
            $groupSubscriber->sub_id = $subscriber->pk();
            $groupSubscriber->save(true);
            if ($this->type == self::TYPE_SUB) {
                $this->count++;
                $this->save(true);
            }
            return true;
        }

        return false;
    }

    public function delete($cache_delete = false)
    {
        if (!$this->loaded())
            return false;

        switch ($this->type) {
            case self::TYPE_BID:
                $bids = SubscriptionBid::model()->where('group_id', '=', $this->pk())->find_all();
                foreach ($bids as $bid) {
                    $bid->delete();
                }
            case self::TYPE_SUB:
                $subs = SubscriberGroup::model()->where('group_id', '=', $this->pk())->find_all();
                foreach ($subs as $sub) {
                    $sub->delete();
                }
                break;
        }
        parent::delete($cache_delete);
    }


    public function whereType($type)
    {
        $this->where('type', '=', $type);
        return $this;
    }


    private function generateHashUrl()
    {
        $hash = Strings::random(5, '0-9a-zA-Z');
        if (!self::hasHash(md5($hash)))
            return $hash;
        else
            return $this->generateHashUrl();

    }


    protected $_primary_key = 'group_id';
    protected $_table_name = 'df_subscription';

    protected $_table_columns_set = array('name', 'url');

    protected $_created_column = array(
        'column' => 'date_cr',
        'format' => TRUE,
    );

    protected $_updated_column = array(
        'column' => 'date_edit',
        'format' => TRUE, // 'd.m.Y H:i'
    );

    protected $_config_key = 'config';

    protected function filters()
    {
        return array(
            TRUE => array(
                array('trim')
            ),
            'url' => array(
                array(array($this, 'url'))
            )
        );
    }

    protected function url($value = null)
    {
        if ($value == null) {
            $value = $this->generateHashUrl();
        }
        $this->hash = md5($value);
        return $value;
    }

    protected function behaviors()
    {
        return array(
            'loggerBehavior' => 'CMS\Core\Behaviors\LoggerBehavior',
        );
    }

    protected $_table_columns = array(
        'group_id' => array(
            'data_type' => 'int unsigned',
            'display' => 11,
            'column_name' => 'group_id',
            'extra' => 'auto_increment',
            'key' => 'PRI'
        ),
        'hash' => array(
            'column_name' => 'hash',
            'data_type' => 'varchar',
            'character_maximum_length' => 32,
            'collation_name' => 'utf8_general_ci',
        ),
        'url' => array(
            'column_name' => 'url',
            'data_type' => 'varchar',
            'character_maximum_length' => 5,
            'collation_name' => 'utf8_general_ci',
        ),
        'name' => array(
            'column_name' => 'name',
            'data_type' => 'varchar',
            'character_maximum_length' => 200,
            'collation_name' => 'utf8_general_ci',
        ),
        'count' => array(
            'column_name' => 'count',
            'data_type' => 'int',
            'display' => 11,
            'column_default' => 0,
        ),
        'config' => array(
            'column_name' => 'config',
            'data_type' => 'text',
            'collation_name' => 'utf8_general_ci',
        ),
        'date_cr' => array(
            'column_name' => 'date_cr',
            'data_type' => 'int',
            'display' => 11,
        ),
        'date_edit' => array(
            'column_name' => 'date_edit',
            'data_type' => 'int',
            'display' => 11,
            'column_default' => 0,
        ),
        'is_name' => array(
            'column_name' => 'is_name',
            'data_type' => 'tinyint unsigned',
            'display' => 1,
            'column_default' => 0,
        ),
        'is_phone' => array(
            'column_name' => 'is_phone',
            'data_type' => 'tinyint unsigned',
            'display' => 1,
            'column_default' => 0,
        ),
        'is_email' => array(
            'column_name' => 'is_email',
            'data_type' => 'tinyint unsigned',
            'display' => 1,
            'column_default' => 0,
        ),
        'is_comment' => array(
            'column_name' => 'is_comment',
            'data_type' => 'tinyint unsigned',
            'display' => 1,
            'column_default' => 0,
        ),
        'type' => array(
            'column_name' => 'type',
            'data_type' => 'enum',
            'options'=> array('bid','sub')
        ),
    );

}