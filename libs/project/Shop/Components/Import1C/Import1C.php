<?php namespace Shop\Components\Import1C;

use Shop\Components\Import1C\interfaces\IImport1C;
use Shop\Components\Import1C\models\StatusRow;

class Import1C implements IImport1C
{
    protected $importFolder;

    public function __construct()
    {
        $this->importFolder = realpath(__DIR__ . '/../../../../../export_tmp');
    }

    public function getImportStatus()
    {
        $importContent = $this->listPath($this->importFolder);

        $list = array();
        foreach ($importContent as $datetime => $folderPath) {
            $list[] = $this->folderModel($datetime, $folderPath);
        }

        return array(
            'list' => $list,
        );
    }

    /* Helpers */
    protected function listPath($path) {
        $files = array_filter(scandir($path), function ($name) {
            return !in_array($name, ['.', '..', '.gitignore', 'clear_lock.php']);
        });
    
        $list = array();
        foreach ($files as $filename) {
            $list[$filename] = realpath(implode('/', [$path, $filename]));
        }
    
        ksort($list);
        return $list;
    }

    protected function folderModel($datetime, $path)
    {
        $model = new StatusRow;

        $model->status = IImport1C::STATUS_SUCCESS;
        $model->datetime = $datetime;

        $folderData = $this->listPath($path);
        $files = array_keys($folderData);

        if (in_array('.error', $files)) {
            $errorFile = $folderData['.error'];
            $model->status = IImport1C::STATUS_ERROR;
            $model->statusMessages[] = file_get_contents($errorFile);
        }

        $importError = in_array('import.xml', $files)
            ? false
            : 'Отсутствует файл import.xml';

        $offersError = in_array('offers.xml', $files)
            ? false
            : 'Отсутствует файл offers.xml';

        if ($importError !== false || $offersError !== false) {
            $model->status = IImport1C::STATUS_ERROR;
            
            $model->statusMessages = array_merge(
                $model->statusMessages,
                array_filter(array(
                    $importError,
                    $offersError
                ))
            );
        }

        return $model;
    }
}
