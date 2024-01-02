<?php
namespace Delorius\Tools;

if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
    define('LOGGER_LINE_FEED', "\r\n");
} else {
    define('LOGGER_LINE_FEED', "\n");
}


interface ILogger
{
    const
        INFO        = 'Info',
        WARNING     = 'Warning',
        ALERT       = 'Alert',
        CRITICAL    = 'Critical',
        DEBUG       = 'Debug',
        ERROR       = 'Error';

    /**
     * Необходимо принять меры немедленно.
     *
     * Example: Entire website down, database unavailable, etc. This should
     * trigger the SMS alerts and wake you up.
     *
     * @param $message
     * @param $status
     * @return mixed
     */
    public function alert($message, $status);

    /**
     * Интересные события
     *
     * @param $message
     * @param $status
     * @return mixed
     */
    public function info($message, $status);

    /**
     * Исключительные случаи, которые не являются ошибками.
     *
     * @param $message
     * @param $status
     * @return mixed
     */
    public function warning($message, $status);

    /**
     * Не требует немедленого выполнения, но должно быть зарегистрировано
     *
     * @param $message
     * @param $status
     * @return mixed
     */
    public function error($message, $status);

    /**
     * Detailed debug information.
     *
     * @param $message
     * @param $status
     * @return mixed
     */
    public function debug($message, $status);

    /**
     * Critical conditions.
     *
     * Example: Application component unavailable, unexpected exception.
     *
     * @param $message
     * @param $status
     * @return mixed
     */
    public function critical($message, $status);

} 