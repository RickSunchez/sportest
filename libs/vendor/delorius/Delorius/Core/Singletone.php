<?php
namespace Delorius\Core;


class Singletone 
{
 
	/**
	 * Хранит экземпляры классов
	 */
	private static $_Instances = array();
 
	/**
	 * Приватный конструктор.
	 * Запрещаем прямое создание объектов
	 */
	private function __construct() {}
 
	/**
	 * Запрещаем клонирование объекта
	 */
	private function __clone() {}
 
	/**
	 *	Возвращает единственный экземпляр класса
	 */
	public static function getInstance()
	{
		/*Узнаем имя класса потомка*/
		$ClassName = \get_called_class();
		/*Если экземпляр этого класса еще не создавался*/
		if (!isset(self::$_Instances[$ClassName]))
		{
			/*Получаем переданные аргументы*/
			$Args = \func_get_args();
			/*Создаем экземпляр класса
			//Если у потомка определен конструктор, то создадим объект передав параметры */
			try
			{
				self::$_Instances[$ClassName] = get(new \ReflectionClass($ClassName))->newInstanceArgs($Args);
			}
			catch(\ReflectionException $E)
			{
				self::$_Instances[$ClassName] = new $ClassName();
			}
		}
		/*Возвращаем экземпляр класса*/
		return self::$_Instances[$ClassName];
	}
}
