<?php
namespace Shop\Store\Behaviors;

use Delorius\Behaviors\ORMBehavior;
use Delorius\Core\Environment;
use Delorius\Core\ORM;
use Delorius\Exception\Error;
use Delorius\Tools\ILogger;
use Shop\Commodity\Helpers\Popular;
use Shop\Store\Component\Status\ACallback;
use Shop\Store\Helper\OrderHelper;

class OrderBehavior extends ORMBehavior
{

    /** @var  int old status */
    protected $status;
    /** @var  bool */
    protected $loaded;
    /** @var bool */
    protected $isChanged;


    public function beforeSave(ORM $orm)
    {
        $original_values = $orm->original_values();
        $this->status = $original_values['status'];
        $this->isChanged = $orm->changed('status');
        $this->loaded = $orm->loaded();
    }

    public function afterSave(ORM $orm)
    {
        if ($this->loaded && $this->isChanged) {

            $config = OrderHelper::getStatusById($orm->status);
            Popular::order($orm->pk(),$orm->status);

            if (isset($config['callback']) && class_exists($config['callback'])) {

                try {
                    $callback = $config['callback'];
                    $class = new $callback($orm);
                    if ($class instanceof ACallback) {
                        $class->run();
                    }

                } catch (Error $e) {
                    /** @var ILogger $logger */
                    $logger = Environment::getContext()->getService('logger');

                    $logger->error(
                        _sf('order_id:{0},status_old:{1},status_new:{2},callback:{3}',
                            $orm->pk(), $this->status, $orm->status, $callback)
                        , 'order-status-error');

                }

            }

        }
    }

}