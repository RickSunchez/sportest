<?php namespace Boat\Store\Cron\Export1C\components\properties;

class Properties
{
    protected $properties = array();

    public function init($importData)
    {
        $propertiesExists = (bool)count($importData->Классификатор->Свойства->Свойство);
        if (!$propertiesExists) {
            return $this->properties;
        }

        foreach ($importData->Классификатор->Свойства->Свойство as $props) {
            $cid = $props->Ид->__toString();
            if (count($props->ВариантыЗначений->Справочник)) {
                foreach ($props->ВариантыЗначений->Справочник as $prop) {
                    $id = $prop->ИдЗначения->__toString();
                    $value = $prop->Значение->__toString();

                    if ($value)
                        $this->properties[$cid][$id] = $value;
                }
            }

            if ($props->ДляТоваров->__toString() == 'true') {
                if (!is_array($this->properties['names'])) {
                    $this->properties['names'] = [];
                }

                $this->properties['names'][$cid] = $props->Наименование->__toString();
            }
        }

        return $this->properties;
    }
}
