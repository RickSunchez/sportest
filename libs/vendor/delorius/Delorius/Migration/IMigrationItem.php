<?php
namespace Delorius\Migration;

use Delorius\Core\ORM;
use Delorius\DataBase\DataBase;

interface IMigrationItem {
    /**
     * Уникальное название
     * @return string
     */
    public function getName();

    /**
     * Проверка Таблицы на изменения
     * @return bool
     */
    public function isChange();

    /**
     * Получить запрос на выполнения изменения
     * @return array
     */
    public function getQuery();

    /**
     * Получаем базу с которой работать будем
     * @return DataBase
     */
    public function getDB();

    /**
     * @return ORM
     */
    public function getModel();
}