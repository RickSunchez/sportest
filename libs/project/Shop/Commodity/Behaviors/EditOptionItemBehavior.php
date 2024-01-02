<?php
namespace Shop\Commodity\Behaviors;

use Delorius\Behaviors\ORMBehavior;
use Delorius\Core\ORM;
use Delorius\DataBase\DB;
use Delorius\Exception\OrmValidationError;
use Shop\Commodity\Entity\Options\Inventory;
use Shop\Commodity\Entity\Options\Item;
use Shop\Commodity\Entity\Options\Variant;


class EditOptionItemBehavior extends ORMBehavior
{
    protected $loaded;
    protected $isInventoryChanged;

    public function afterDelete(ORM $orm)
    {
        $vars = Variant::model()->where('option_id', '=', $orm->pk())->find_all();
        foreach ($vars as $var) {
            $var->delete();
        }

        if ($orm->inventory == 1) {
            $invs = Inventory::model()->where('goods_id', '=', $orm->goods_id)->find_all();
            foreach ($invs as $inv) {
                $inv->delete();
            }
        }
    }

    public function beforeSave(ORM $orm)
    {
        $this->isInventoryChanged = $orm->changed('inventory');
        $this->loaded = $orm->loaded();

        if ($orm->type == Item::TYPE_TEXT || $orm->type == Item::TYPE_VARCHAR) {
            $orm->inventory = 0;
        }
    }

    public function afterSave(ORM $orm)
    {
        $table = Variant::model();

        if (!$this->loaded && $orm->type == Item::TYPE_FLAG) {

            DB::insert($table->table_name(), array(
                'option_id', 'goods_id', 'pos', 'status', 'name', 'inventory'
            ))
                ->values(array($orm->pk(), $orm->goods_id, 1, 1, 'Да', $orm->inventory))
                ->values(array($orm->pk(), $orm->goods_id, 0, 1, 'Нет', $orm->inventory))
                ->execute($table->db_config());

        }

        if ($this->loaded) {

            if ($this->isInventoryChanged && !$orm->inventory) {
                $invs = Inventory::model()->where('goods_id', '=', $orm->goods_id)->find_all();
                foreach ($invs as $inv) {
                    $inv->delete();
                }
            }

            DB::update($table->table_name())
                ->where('option_id', '=', $orm->pk())
                ->set(array('inventory' => $orm->inventory))
                ->set(array('required' => $orm->required))
                ->execute($table->db_config());
        }
    }

    /**
     * @param array $value
     * @return bool|int ID
     * @throws \Delorius\Exception\Error
     */
    public function addVariant($value)
    {
        try {
            $ormVariant = new Variant($value[Variant::model()->primary_key()]);
            if ($value['delete'] == 1) {
                if ($ormVariant->loaded()) {
                    $ormVariant->delete();
                }
                return true;
            }
            $ormVariant->values($value);
            $ormVariant->option_id = $this->getOwner()->pk();
            $ormVariant->goods_id = $this->getOwner()->goods_id;
            $ormVariant->inventory = $this->getOwner()->inventory;
            $ormVariant->required = $this->getOwner()->required;
            $ormVariant->save(true);
            return $ormVariant->pk();
        } catch (OrmValidationError $e) {
            return false;
        }
    }


}