<?php
namespace Shop\Store\Controller;

use Delorius\Application\UI\Controller;
use Delorius\Utils\FileSystem;
use Delorius\Utils\Strings;

class Import1cController  extends Controller
{
    /**
     * @var \Delorius\Tools\ILogger
     * @service logger
     * @inject
     */
    public $_logger;

    protected $_static_iterator = false;

    protected $_input_file_size = 8;

    protected $_valid_user = array(
        'username' => 'user',
        'password' => '1234'
    );

    protected $_authenticate = false;

    public function importAction($type, $mode, $filename) {

        $cookie_id = uniqid();
        $type = $_REQUEST['type'];
        $mode = $_REQUEST['mode'];

        if( $mode == 'checkauth' ) {
            echo ("success\n");
            echo ("c-conn-import\n");
            echo ($cookie_id);
        }
        if( $mode == 'init' ) {
            echo("zip=no\n");
            echo("file_limit=".(1024 * 1024 * $this->_input_file_size)."\n");
        }
        if( $mode == 'file' ) {
            $pathinfo = pathinfo( $filename );

            $exportPath   = $this->container->getParameters('path.export');
            file_put_contents( $exportPath . '/.lock', time());
            FileSystem::createDir($exportPath . '/' . $pathinfo['dirname']);

            $file = $exportPath . '/' . ( Strings::length($pathinfo['dirname']) >2  ? $pathinfo['dirname'] . '/' : null). $pathinfo['basename'];
            $this->_logger->info($file, 'import file');
            if(file_exists($file)) {
                if(
                    strpos($pathinfo['basename'], 'import') !== false
                    OR
                    strpos($pathinfo['basename'], 'offers') !== false
                ) {
                    $this->_logger->info('generating new file', 'import.xml||offer.xml');
                    $i = 0;
                    while(file_exists($file)) { ++$i;
                        $file = $exportPath . '/' . $i . '/' . $pathinfo['basename'];
                    }
                    FileSystem::createDir($exportPath . '/' . $i );
                    $file = $exportPath . '/' . $i . '/' . $pathinfo['basename'];
                }
            }
            $f = fopen($file, 'a');
            if (!$f) {
                return false;
            }
            fwrite($f, file_get_contents('php://input'));
            fclose($f);
            clearstatcache();
            echo "success\n";
        }
        if($mode == 'test_connection') {
            echo "success\n";
        }
        if($mode == 'import') {

            echo "success\n";
            $this->_logger->info('finished', 'import');
        }
        if($mode == 'get_dir') {
            echo "success\n";
        }
        exit;
    }

}