<?php
namespace CMS\Core\Behaviors;

use CMS\Core\Entity\Options;
use CMS\Core\Helper\Helpers;
use Delorius\Behaviors\ORMBehavior;
use Delorius\Core\ORM;
use Delorius\DataBase\DB;
use Delorius\Exception\OrmValidationError;

class OptionsBehavior extends ORMBehavior
{

    public function afterDelete(ORM $orm)
    {
        $options = $this->getOptions();
        foreach ($options as $opt) {
            DB::delete(Options::model()->table_name())
                ->where('id', '=', $opt['id'])
                ->execute(Options::model()->db_config());
        }
        Options::model()->cache_delete();
    }

    /**
     * @param null|string|array $code
     * @param bool $cached
     * @return ORM|\Delorius\DataBase\Result
     */
    public function getOptions($code = null, $cached = false)
    {
        $options = Options::model()
            ->select()
            ->where('target_id', '=', $this->getOwner()->pk())
            ->where('target_type', '=', Helpers::getTableId($this->getOwner()))
            ->sort();

        if (is_array($code)) {
            $options->where('code', 'IN', $code);
            $multi = true;
        } elseif (is_scalar($code)) {
            $multi = false;
            $options->where('code', '=', $code);
        } else {
            $multi = true;
        }

        if ($cached) {
            $options->cached($cached);
        }

        if ($multi)
            return $options->find_all();
        else
            return $options->find();

    }

    /**
     * @param array $value
     * @return array|bool
     */
    public function addOption(array $value)
    {
        if (!$this->getOwner()->loaded()) {
            return false;
        }

        try {
            $opt = new Options($value[Options::model()->primary_key()]);
            if ($value['delete'] == 1) {
                if ($opt->loaded()) {
                    $opt->delete();
                }
                return true;
            }
            $opt->values($value);
            $opt->target_id = $this->getOwner()->pk();
            $opt->target_type = Helpers::getTableId($this->getOwner());
            $opt->save(true);
            return $opt->as_array();
        } catch (OrmValidationError $e) {
            return false;
        }

    }

    /**
     * @param string $code
     * @param string $name
     * @param string $value
     * @param int $pos
     * @return bool
     * @throws \Delorius\Exception\Error
     */
    public function setOption($code, $name, $value, $pos = 0)
    {
        $option = Options::model()
            ->where('target_id', '=', $this->getOwner()->pk())
            ->where('target_type', '=', Helpers::getTableId($this->getOwner()))
            ->where('code', '=', $code)
            ->find();

        if ($value == null || $option->loaded()) {
            $option->delete();
            return true;
        }

        if ($name == null || $option->loaded()) {
            $option->delete();
            return true;
        }

        try {
            $option->code = $code;
            $option->name = $name;
            $option->value = $value;
            $option->pos = $pos;
            $option->target_id = $this->getOwner()->pk();
            $option->target_type = Helpers::getTableId($this->getOwner());
            $option->save();
            return true;
        } catch (OrmValidationError $e) {
            return false;
        }
    }


    /**
     * @param array $options (code,name,value,pos,id)
     * @throws \Delorius\Exception\Error
     */
    public function mergeOptions(array $options, $name = true)
    {
        foreach ($options as $opt) {

            $finder = Options::model()
                ->where('target_id', '=', $this->getOwner()->pk())
                ->where('target_type', '=', Helpers::getTableId($this->getOwner()))
                ->where('code', '=', $opt['code']);

            if ($name) {
                $finder->where('name', '=', $opt['name']);
            }

            $orm = $finder->find();

            if (!$orm->loaded()) {
                $this->addOption($opt);
            } else {
                if ($opt['name']) {
                    $orm->value = $opt['value'];
                    $orm->save();
                } else {
                    $orm->delete();
                }
            }
        }
    }

} 