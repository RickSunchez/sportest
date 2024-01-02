<?php
namespace Boat\Store\Controller;

use Boat\Store\Component\Exchange1c\OrderXML;
use Delorius\Application\UI\Controller;
use Delorius\DataBase\DB;
use Delorius\Utils\FileSystem;
use Delorius\Utils\Strings;
use Shop\Commodity\Entity\CollectionProduct;
use Shop\Commodity\Entity\CollectionProductItem;
use Shop\Commodity\Entity\Goods;
use Shop\Store\Entity\Order;

class Import1cController extends Controller
{
    /**
     * @var \Delorius\Tools\ILogger
     * @service logger
     * @inject
     */
    public $_logger;

    protected $_static_iterator = false;

    protected $_input_file_size = 20;

    protected $_valid_user = array(
        'username' => 'user',
        'password' => '1234'
    );

    protected $_authenticate = false;

    public function importAction($type, $mode, $filename)
    {
        logger('[import] run importAction');

        ini_set("allow_url_fopen", true);
        $cookie_id = uniqid();
        $type = $_REQUEST['type'];
        $mode = $_REQUEST['mode'];
//        $this->_logger->info(var_export($_REQUEST, true), 'import');

        if ($type != 'catalog') {
            echo "failure\n";
            exit;
        }

        if ($mode == 'checkauth') {
            echo("success\n");
            echo("c-conn-import\n");
            echo($cookie_id);
        }
        if ($mode == 'init') {
            echo("zip=no\n");
            echo("file_limit=" . (1024 * 1024 * $this->_input_file_size) . "\n");
        }
        if ($mode == 'file') {
            logger('[import] mode file');

            $pathinfo = pathinfo($filename);

            // $exportPath = $this->container->getParameters('path.export');
            // file_put_contents($exportPath . '/.lock', time());
            // FileSystem::createDir($exportPath . '/' . $pathinfo['dirname']);

//             $file = $exportPath . '/' . (Strings::length($pathinfo['dirname']) > 2 ? $pathinfo['dirname'] . '/' : null) . $pathinfo['basename'];
// //            $this->_logger->info($file, 'import file');
//             if (file_exists($file)) {
//                 if (
//                     strpos($pathinfo['basename'], 'import') !== false
//                     OR
//                     strpos($pathinfo['basename'], 'offers') !== false
//                 ) {
//                     $this->_logger->info('generating new file', 'import.xml||offer.xml');
//                     $i = 0;
//                     while (file_exists($file)) {
//                         ++$i;
//                         $file = $exportPath . '/' . $i . '/' . $pathinfo['basename'];
//                     }
//                     FileSystem::createDir($exportPath . '/' . $i);
//                     $file = $exportPath . '/' . $i . '/' . $pathinfo['basename'];
//                 }
//             }
//             $f = fopen($file, 'a');
//             if (!$f) {
//                 logger('[import] file not exists exit');
//                 return false;
//             };

            // logger('[import] write file: ' . $file);
            $code = file_get_contents('php://input');

            // @note парсим дату формирования, она должна совпасть у обоих файлов
            libxml_use_internal_errors(true);
            $xmlObject = simplexml_load_string($code);
            $data = null;
            if ($xmlObject) {
                $jsonFormatData = json_encode($xmlObject);
                $data = json_decode($jsonFormatData, true);
            }

            $fileDate = null;
            if (is_array($data)) {
                $fileDate = key_exists('@attributes', $data)
                ? (
                    key_exists('ДатаФормирования', $data['@attributes'])
                        ? $data['@attributes']['ДатаФормирования']
                        : null
                )
                : null;
            }

            if (!$fileDate) {
                $exportTmp = $this->container->getParameters('path.export_tmp');
                $folderName = date('Y-m-d_H-i-s') . '-no-time';
                $folderPath = implode('/', [
                    $exportTmp,
                    $folderName,
                ]);
                FileSystem::createDir($folderPath);

                $fileName = $pathinfo['basename'];
                $filePath = implode('/', [
                    $folderPath,
                    $fileName
                ]);

                file_put_contents($filePath, $code);
                echo "fail\n";
                exit();
            }

            $exportTmp = $this->container->getParameters('path.export_tmp');
            $foldername = date('Y-m-d_H-i-s', strtotime($fileDate));
            $folderPath = implode('/', [
                $exportTmp,
                $foldername
            ]);
            FileSystem::createDir($folderPath);

            $fileName = $pathinfo['basename'];
            $filePath = implode('/', [
                $folderPath,
                $fileName
            ]);

            logger('[import] write file: ' . $filePath);
            file_put_contents($filePath, $code);

            $scriptPath = realpath(__DIR__ . '/../../../../../cron');

            $result = null;
            $output = exec(
                implode(' ', [
                    $scriptPath . '/run_export.sh',
                    $scriptPath . '/export_boat.php',
                ]), 
                $result
            );

            echo "success\n";
        }
        if ($mode == 'test_connection') {
            echo "success\n";
        }
        if ($mode == 'import') {

            echo "success\n";
            $this->_logger->info('finished', 'import');
        }
        if ($mode == 'get_dir') {
            echo "success\n";
        }
        exit;
    }

    public function ordersAction($type, $mode, $filename)
    {
        $cookie_id = uniqid();
        $type = $_REQUEST['type'];
        $mode = $_REQUEST['mode'];
//        $this->_logger->info(var_export($_REQUEST, true), 'orders');


        if ($type != 'sale') {
//            $this->_logger->info('failure', 'orders');
            echo "failure\n";
            exit;
        }

        if ($mode == 'checkauth') {
//            $this->_logger->info('checkauth', 'orders');
            echo("success\n");
            echo("c-conn-import\n");
            echo($cookie_id);
        }
        if ($mode == 'init') {
            echo("zip=no\n");
            echo("file_limit=" . (1024 * 1024 * $this->_input_file_size) . "\n");
        }
        if ($mode == 'file') {
            echo("success\n");
        }
        if ($mode == 'query') {
//            $this->_logger->info('query', 'orders');

            $orders = Order::model()
                ->order_pk()
                ->or_where_open()
                ->or_where('exchange_status', '=', EXCHANGE_NOT)
                ->or_where('exchange_status', '=', EXCHANGE_QUERY)
                ->or_where_close()
                ->find_all();

//            if (count($orders) == 0) {
//             //   $this->_logger->info('query 0', 'orders');
//                exit;
//            }

            $ids = array();
            foreach ($orders as $order) {
                $ids[] = $order->pk();
            }

            $collections = $this->getGoodsCollections($orders);

            if(count($ids)) {
//                $this->_logger->info('query 1', 'orders');
                DB::update(Order::model()->table_name())
                    ->value('exchange_status', EXCHANGE_QUERY)
                    ->where('order_id', 'IN', $ids)
                    ->execute(Order::model()->db_config());
            }

            $this->httpResponse->setHeader('Content-Type', 'text/xml');
            $this->httpResponse->setHeader('Charset', 'UTF-8');

            $xml = new OrderXML();
            $xml->create($orders, $collections);
            $result =  $xml->saveXML();

            echo $result;

            $this->_logger->info($result, 'orders');
            $this->_logger->info('query end', 'orders');
        }
        if ($mode == 'success') {
//            $this->_logger->info('success', 'orders');
            $orders = Order::model()
                ->select('order_id')
                ->where('exchange_status', '=', EXCHANGE_QUERY)
                ->find_all();

            if (count($orders) == 0) {
                $this->_logger->info('success 0', 'orders');
                echo "success\n";
                exit;
            }

            $ids = array();
            foreach ($orders as $order) {
                $ids[] = $order['order_id'];
            }
//            $this->_logger->info('success 1', 'orders');
            DB::update(Order::model()->table_name())
                ->value('exchange_status', EXCHANGE_FINISH)
                ->where('order_id', 'IN', $ids)
                ->execute(Order::model()->db_config());

            echo "success\n";
//            $this->_logger->info('finished', 'orders');
        }
        if ($mode == 'get_dir') {
            echo "success\n";
        }
        exit;
    }

    protected function getGoodsCollections($orders)
    {
        $goodsIds = array();
        foreach ($orders as $order) {
            foreach ($order->getItems() as $item) {
                $goodsIds[] = $item->goods_id;
            }
        }

        if (empty($goodsIds)) {
            return null;
        }

        $goods = Goods::model()
            ->where('goods_id', 'IN', $goodsIds)
            ->find_all();
        if (count($goods) == 0) {
            return null;
        }

        $goodsMap = array();
        foreach ($goods as $item) {
            $goodsMap[$item->goods_id] = $item->external_id;
        }

        $collectionProducts = CollectionProductItem::model()
            ->where('product_id', 'IN', $goodsIds)
            ->find_all();
        if (count($collectionProducts) == 0) {
            return null;
        }

        $prodCollsIds = array();
        foreach ($collectionProducts as $collProd) {
            $prodCollsIds[$collProd->product_id] = $collProd->coll_id;
        }

        if (empty($prodCollsIds)) {
            return null;
        }

        $collections = CollectionProduct::model()
            ->where('id', 'IN', $prodCollsIds)
            ->find_all();
        if (count($collections) == 0) {
            return null;
        }
    
        $collectionExternals = [];
        foreach ($collections as $collection) {
            $collectionExternals[$collection->id] = $collection->external_id;
        }


        $goodsCollections = [];
        foreach ($collectionExternals as $collectionId => $collectionExternal) {
            $pci = array_filter($prodCollsIds, function ($item) use ($collectionId) {
                return $item == $collectionId;
            });

            if (count($pci) == 0) {
                continue;
            }

            $goodsIds = array_keys($pci);
            foreach ($goodsIds as $id) {
                $goodsEId = $goodsMap[$id];
                $goodsCollections[$goodsEId] = $collectionExternal;
            }
        }
        
        return $goodsCollections;
    }

}