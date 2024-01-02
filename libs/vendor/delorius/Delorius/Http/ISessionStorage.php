<?php
namespace Delorius\Http;

/**
 * User session storage. @see http://php.net/session_set_save_handler
 * @deprecated since PHP 5.4, use \SessionHandlerInterface
 */
interface ISessionStorage
{

	function open($savePath, $sessionName);

	function close();

	function read($id);

	function write($id, $data);

	function remove($id);

	function clean($maxlifetime);

}
