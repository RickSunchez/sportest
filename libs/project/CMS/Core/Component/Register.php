<?php
namespace CMS\Core\Component;

use Delorius\Core\Object;
use Delorius\Core\ORM;
use Delorius\DataBase\DB;
use Delorius\Exception\OrmValidationError;
use Delorius\Http\IRequest;
use Delorius\Security\User;
use Delorius\Tools\ILogger;

class Register extends Object
{
    /**
     * @var User
     * @service user
     */
    protected $user;

    /**
     * @var IRequest
     * @service httpRequest
     */
    protected $httpRequest;

    /**
     * @var ILogger
     * @service logger
     */
    protected $logger;

    /**
     * @var string
     */
    protected $path;

    const
        SPACE_SITE = 1,
        SPACE_CABINET = 2,
        SPACE_CRON = 3,
        SPACE_ADMIN = 4,

        TYPE_ATTENTION = 10,
        TYPE_INFO = 11,
        TYPE_ERROR = 12;

    public function __construct(
        $path,
        IRequest $httpRequest,
        User $user,
        ILogger $ILogger)
    {
        $this->path = $path;
        $this->httpRequest = $httpRequest;
        $this->user = $user;
        $this->logger = $ILogger;
    }

    protected static function replace_text($text, $orm, $keywords)
    {
        $values = array_values($keywords);
        $keys = array_map(
            function ($key) {
                return '[' . $key . ']';
            },
            array_keys($keywords)
        );
        if ($orm) {
            preg_match_all('/\[(.*?)\]/', $text, $results);
            $arr = $orm->as_array();
            for ($i = 0; $i < count($results[1]); $i++) {
                if (isset($arr[$results[1][$i]])) {
                    $keys[] = $results[0][$i];
                    $values[] = $arr[$results[1][$i]];
                }
            }
        }

        return str_replace(
            $keys, $values,
            $text
        );
    }

    /**
     * @param int $_type
     * @param int $_space
     * @param string $text
     * @param ORM|null $orm
     * @param array $array
     * @return void
     */
    public function add($_type, $_space, $text, $orm = null, $array = array())
    {
        try {
            $register = new \CMS\Core\Entity\Register;

            if ($this->user->isLoggedIn()) {
                $register->user_namespace = $this->user->getStorage()->getNamespace();
                $register->user_id = $this->user->getId();
            }

            if ($orm instanceof ORM) {
                $register->target_type = $orm->table_name();
                $register->target_id = $orm->pk();
            }

            $register->text = self::replace_text($text, $orm, $array);
            $register->ip = $this->httpRequest->getRemoteAddress();
            $register->space = $_space;
            $register->type = $_type;
            $register->save();
            $this->logger->info(_sf('{0}: {1}', $register->getTypeName(), $register->text), 'register');
        } catch (OrmValidationError $e) {
            $this->logger->error($e->getErrorsMessage(), 'register');
        }
    }

    private function generateRow(\CMS\Core\Entity\Register $register)
    {
        $result = '';
        $result .= $register->getSpaceName() . ';';
        $result .= $register->getTypeName() . ';';
        $result .= $register->ip . ';';
        $result .= $register->user_id . ';';
        $result .= $register->user_namespace . ';';
        $result .= $register->target_id == 0 ? ';' : $register->target_id . ';';
        $result .= $register->target_type . ';';
        $result .= $register->text . ';';
        $date = $register->date_cr;
        $result .= date('d.m.Y', $date) . ";";
        $result .= date('H:i:s', $date) . ";\n";
        return $result;
    }

    /**
     * Очистка данных
     */
    public function clear()
    {
        DB::query(null, _sf('TRUNCATE `{0}` ', \CMS\Core\Entity\Register::model()->table_name()))->execute();
    }

    /**
     * Конвертирования в CSV
     * @throws \Delorius\Exception\Error
     */
    public function createArchive()
    {
        try {
            $file_content = chr(0xEF) . chr(0xBB) . chr(0xBF);
            $orm = \CMS\Core\Entity\Register::model();
            $fields = array_slice(array_keys($orm->table_columns()), 1);
            $file_content .= implode(';', $fields) . ";time\n";
            $result = $orm->sort()->find_all();
            foreach ($result as $data) {
                $file_content .= $this->generateRow($data);
            }
            file_put_contents($this->path . '/register_' . date('d-m-Y_H-i') . '.csv', $file_content);
        } catch (OrmValidationError $e) {
            $this->logger->error($e->getErrorsMessage(), 'register.createArchive');
        }
    }
}
