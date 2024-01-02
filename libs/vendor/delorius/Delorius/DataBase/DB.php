<?php
namespace Delorius\DataBase;

/**
 * Provides a shortcut to get DataBase related objects for [making queries](../DataBase/query).
 *
 * Shortcut     | Returned Object
 * -------------|---------------
 * [`DB::query()`](#query)   | [DataBase_Query]
 * [`DB::insert()`](#insert) | [DataBase_Query_Builder_Insert]
 * [`DB::select()`](#select),<br />[`DB::select_array()`](#select_array) | [DataBase_Query_Builder_Select]
 * [`DB::update()`](#update) | [DataBase_Query_Builder_Update]
 * [`DB::delete()`](#delete) | [DataBase_Query_Builder_Delete]
 * [`DB::expr()`](#expr)     | [DataBase_Expression]
 *
 * You pass the same parameters to these functions as you pass to the objects they return.
 *
 * @package    Kohana/DataBase
 * @category   Base
 * @author     Kohana Team
 * @copyright  (c) 2009 Kohana Team
 * @license    http://kohanaphp.com/license
 */

class DB
{

    /**
     * Create a new [DataBase_Query] of the given type.
     *
     *     // Create a new SELECT query
     *     $query = DB::query(DataBase::SELECT, 'SELECT * FROM users');
     *
     *     // Create a new DELETE query
     *     $query = DB::query(DataBase::DELETE, 'DELETE FROM users WHERE id = 5');
     *
     * Specifying the type changes the returned result. When using
     * `DataBase::SELECT`, a [DataBase_Query_Result] will be returned.
     * `DataBase::INSERT` queries will return the insert id and number of rows.
     * For all other queries, the number of affected rows is returned.
     *
     * @param   integer  $type  type: DataBase::SELECT, DataBase::UPDATE, etc
     * @param   string   $sql   SQL statement
     * @return  DataBase/Query
     */
    public static function query($type, $sql)
    {
        return new Query($type, $sql);
    }

    /**
     * Create a new [DataBase_Query_Builder_Select]. Each argument will be
     * treated as a column. To generate a `foo AS bar` alias, use an array.
     *
     *     // SELECT id, username
     *     $query = DB::select('id', 'username');
     *
     *     // SELECT id AS user_id
     *     $query = DB::select(array('id', 'user_id'));
     *
     * @param   mixed   $columns  column name or array($column, $alias) or object
     * @return  \Delorius\DataBase\Query\Builder\Select
     */
    public static function select($columns = NULL)
    {
        return new \Delorius\DataBase\Query\Builder\Select(func_get_args());
    }

    /**
     * Create a new [DataBase_Query_Builder_Select] from an array of columns.
     *
     *     // SELECT id, username
     *     $query = DB::select_array(array('id', 'username'));
     *
     * @param   array   $columns  columns to select
     * @return  \Delorius\DataBase\Query\Builder\Select
     */
    public static function select_array(array $columns = NULL)
    {
        return new \Delorius\DataBase\Query\Builder\Select($columns);
    }

    /**
     * Create a new [DataBase_Query_Builder_Insert].
     *
     *     // INSERT INTO users (id, username)
     *     $query = DB::insert('users', array('id', 'username'));
     *
     * @param   string  $table    table to insert into
     * @param   array   $columns  list of column names or array($column, $alias) or object
     * @return  \Delorius\DataBase\Query\Builder\Insert
     */
    public static function insert($table = NULL, array $columns = NULL)
    {
        return new \Delorius\DataBase\Query\Builder\Insert($table, $columns);
    }

    /**
     * Create a new [DataBase_Query_Builder_Update].
     *
     *     // UPDATE users
     *     $query = DB::update('users');
     *
     * @param   string  $table  table to update
     * @return  \Delorius\DataBase\Query\Builder\Update
     */
    public static function update($table = NULL)
    {
        return new \Delorius\DataBase\Query\Builder\Update($table);
    }

    /**
     * Create a new [DataBase_Query_Builder_Delete].
     *
     *     // DELETE FROM users
     *     $query = DB::delete('users');
     *
     * @param   string  $table  table to delete from
     * @return  \Delorius\DataBase\Query\Builder\Delete
     */
    public static function delete($table = NULL)
    {
        return new \Delorius\DataBase\Query\Builder\Delete($table);
    }

    /**
     * Create a new [DataBase Expression] which is not escaped. An expression
     * is the only way to use SQL functions within query builders.
     *
     *     $expression = DB::expr('COUNT(users.id)');
     *     $query = DB::update('users')->set(array('login_count' => DB::expr('login_count + 1')))->where('id', '=', $id);
     *     $users = ORM::factory('user')->where(DB::expr("BINARY `hash`"), '=', $hash)->find();
     *
     * @param   string  $string  expression
     * @param   array   parameters
     * @return  \Delorius\DataBase\Expression
     */
    public static function expr($string, $parameters = array())
    {
        return new \Delorius\DataBase\Expression($string, $parameters);
    }

}