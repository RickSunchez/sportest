<?php namespace Shop\Components\Import1C\interfaces;

interface IImport1C
{
    const STATUS_ERROR = 0;
    const STATUS_SUCCESS = 1;

    public function getImportStatus();
}
