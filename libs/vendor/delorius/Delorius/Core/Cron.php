<?php
namespace Delorius\Core;
ignore_user_abort(true);

use CMS\Core\Component\Register;
use Delorius\Exception\Error;
use Delorius\Utils\FileSystem;

/**
 * Требуется переделка крона
 * Class Cron
 * @package Delorius\Core
 */
abstract class Cron
{

    protected $nameCronProcess;
    protected $path;
    protected $lockFile;
    protected $container;
    protected $isLogger = false;

    public function __construct($nameCronProcess, $isLogger = false)
    {
        $this->isLogger = $isLogger;
        $this->container = Environment::getContext();
        $this->nameCronProcess = $nameCronProcess;
        $this->path = $this->container->getParameters('path.cron');
        if (!is_dir($this->path)) {
            FileSystem::createDir($this->container->getParameters('path.temp') . '/cron');
        }
        if (!$this->lockFile) {
            $this->lockFile = fopen($this->path . '/' . $this->nameCronProcess, 'a');
        }
        $this->log('Cron process started: ' . $nameCronProcess);
    }

    /**
     * Делает запись в лог
     *
     * @param  string $sMsg Сообщение для записи в лог
     */
    public function log($sMsg)
    {
        if ($this->isLogger) {
            $this->container->getService('logger')->info($sMsg, 'cron');
        }
    }

    /**
     * Проверяет уникальность создаваемого процесса
     *
     * @return bool
     */
    public function isLock()
    {
        return ($this->lockFile && !flock($this->lockFile, LOCK_EX | LOCK_NB));
    }

    /**
     * Снимает блокировку на повторный процесс
     *
     * @return bool
     */
    public function unsetLock()
    {
        return ($this->lockFile && @flock($this->lockFile, LOCK_UN));
    }

    /**
     * Основной метод крон-процесса.
     * Реализует логику работы крон процесса с последующей передачей управления на пользовательскую функцию
     *
     * @return string
     */
    public function Exec()
    {
        /**
         * Если выполнение процесса заблокирован, завершаемся
         */
        if ($this->isLock()) {
            throw new Error('Try to exec already run process');
        }

        /**
         * Здесь мы реализуем дополнительную логику:
         * логирование вызова, обработка ошибок,
         * буферизация вывода.
         */
        ob_start();
        $this->client();
        /**
         * Получаем весь вывод функции.
         */
        $sContent = ob_get_contents();
        ob_end_clean();
        return $sContent;
    }

    /**
     * Завершение крон-процесса
     */
    public function shutdown()
    {
        $this->unsetLock();
        $this->log('Cron process ended: ' . $this->nameCronProcess);
    }

    /**
     * Вызывается при уничтожении объекта
     */
    public function __destruct()
    {
        $this->shutdown();
    }

    /**
     * Завершения выполнения запроса, вызывается вручном режиме
     * @param string $name
     * @param int $type
     */
    public function isEnd($name, $type = Register::TYPE_INFO)
    {

        $this->container->getService('register')->add(
            $type,
            Register::SPACE_CRON,
            '[name]',
            null,
            array('name' => $name)
        );
    }

    /**
     * Клиентская функция будет переопределятся в наследниках класса
     * для обеспечивания выполнения основного функционала.
     */
    abstract protected function client();
}
