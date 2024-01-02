<?php
namespace Delorius\Migration;


interface IMigrationInsert {
    /**
     * вставить данные
     * @param $insert
     * @return $this
     */
    public function insert($insert);

    /**
     * Проверить пустая ли таблица
     * @return bool
     */
    public function isEmptyTable();

    /**
     * вставить все данные
     * @return void
     */
    public function insetTable();
}