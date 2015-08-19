<?php
abstract class ActiveRecord {

    /**
     * database connection.
     */
    private static $connection = null;

    /**
     * database connection configurations.
     */
    private static $configurations = array();

    /**
     * cache active record objects.
     * first level cache.
     */
    private static $cached_active_records = array();

    /**
     * cache all table names for all models.
     */
    private static $cached_table_names = array();

    /**
     * cache all table columns info.
     */
    private static $cached_table_columns = array();

    /**
     * for caching static properties of models.
     */
    private static $cached_static_properties_of_models = array();

    /**
     * cache ancestor classes of models.
     */
    private static $cached_model_ancestors = array();

    /**
     * cache associations by model name.
     */
    private static $cached_associations = array();

    /**
     * cache associations keys by model name.
     */
    private static $cached_association_keys = array();

    /**
     * for nested transactions.
     */
    private static $transaction_started = false;

    /**
     * default table name is pluralized version of class name.
     */
    protected static $table_name = null;

    /**
     * table name prefix, can be overrided by subclasses.
     */
    protected static $table_name_prefix = '';

    /**
     * table name suffix, can be overrided by subclasses.
     */
    protected static $table_name_suffix = '';

    /**
     * defines the primary key field -- can be overridden in subclasses.
     * using logical primary key, don't using compsite id.
     */
    protected static $primary_key = 'id';

    /**
     * Sets the name of the sequence class to use when generating ids to the given value.
     * This is required for Oracle and is useful for any database which relies on sequences for primary key generation.
     */
    protected static $sequence_class_name = null;

    /**
     * has one association configure.
     */
    protected static $has_one = array();

    /**
     * has many assocition configure.
     */
    protected static $has_many = array();

    /**
     * belongs to association configure.
     */
    protected static $belongs_to = array();

    /**
     * attributes listed as readonly can be set for a new record, but will be ignored in database updates afterwards.
     */
    public static $readonly_attributes = array();

    public static function establish_connection($config) {
        self::validate_connection_params($config);
        $adapter = strtolower($config['adapter']);
        require_once('database_adapters' . DS . $adapter . '.php');
        return call_user_func(array($config['adapter'], 'establish_connection'), $config['host'], $config['username'], $config['password'], $config['database'], $config['port'], $config['encoding']);
    }

    public static function get_connection() {
        if(self::$connection == null) {
            if(self::$configurations == null) {
                self::load_configurations();
            }
            self::$connection = self::establish_connection(self::$configurations);
        }
        return self::$connection;
    }

    /**
     * Find method first parameter may be:
     * 1. id.
     * 2. ids array.
     * 3. 'all'.
     * 4. 'first'.
     * 5. 'last'.
     *
     * This method second parameter options keys list
     * 1. select: sql select fields list.
     * 2. joins: join tables.
     * 3. confitions(where): sql conditions setting.
     * 4. group:
     * 5. having:
     * 6. order:
     * 7. limit:
     * 8. offset:
     * 9. lock: string such as 'LOCK IN SHARE MODE' or true ('for update').
     * 10. include: for preload configed association models.
     *     include option avoid 1 + N sql problem using LEFT OUTER JOIN the included models.
     *     include option can not be used to include self-join association.
     *     include option will ignore 'select' and 'joins' in $options.
     */
    public static function find($ids, $options = array()) {
        self::validate_find_options($ids, $options);
        $model_name = self::get_model_name();
        switch($ids) {
            case 'first' :
                return self::find_first($model_name, $options);
            case 'last' :
                return self::find_last($model_name, $options);
            case 'all' :
                return self::find_all($model_name, $options);
            default :
                return self::find_from_ids($model_name, $ids, $options);
        }
    }

    /**
     * A convenience wrapper for find('first', $options).
     * If no record found will return false, not return null for sake association lazy load.
     */
    public static function first($options = array()) {
        return self::find('first', $options);
    }

    /**
     * A convenience wrapper for find('last', $options).
     * If no record found will return false, not return null for sake association lazy load.
     */
    public static function last($options = array()) {
        return self::find('last', $options);
    }

    /**
     * A convenience wrapper for find('all', $options).
     * If no record found will return false, not return null for sake association lazy load.
     */
    public static function all($options = array()) {
        return self::find('all', $options);
    }

    /**
     * This is an alias for find('all', $options).
     */
    public static function select($options = array()) {
        return self::all($options);
    }

    /**
     * Find results using complicated sql, Usally for select database operation.
     * Don't use this method to update or delete operation.
     * If $direct is true, will return query resultset by sql directly.
     */
    public static function find_by_sql($sql, $direct = false) {
        $records = self::fetch_rows($sql);
        if($direct) {
            return $records;
        }
        $model_name = self::get_model_name();
        return self::get_model_objects($model_name, $records);
    }

    /**
     * Create a new active record object using giving $attributes.
     * return false object if create failure.
     */
    public static function create($attributes) {
        $model_name = self::get_model_name();
        $object = new $model_name($attributes);
        if($object->save()) {
            return $object;
        } else {
            return false;
        }
    }

    /**
     * delete rows by id or array of ids.
     * callbacks will run such as before_destroy and after_destroy.
     */
    public static function delete($ids) {
        $objects = self::find($ids);
        if(is_array($objects)) {
            $results = array();
            foreach($objects as $object) {
                $results[] = $object->destroy();
            }
            return $results;
        } else {
            $result = $objects->destroy();
            return $result;
        }
    }

    public static function update($id, $attributes) {
        $object = self::find($id);
        return $object->update_attributes($attributes);
    }

    /**
     * $options is hash array and with keys:
     * 1. conditions
     * 2. order
     * 3. limit
     */
    public static function delete_all($options = array()) {
        $model_name = self::get_model_name();
        $sql = self::construct_delete_sql($model_name, $options);
        self::transaction_start();
        $result = self::get_connection()->delete($sql);
        self::transaction_commit();
        self::remove_objects_from_caches($model_name, $options);
        return $result;
    }

    public static function update_all($updates, $options = array()) {
        $model_name = self::get_model_name();
        $sql = self::construct_update_sql($model_name, $updates, $options);
        self::transaction_start();
        $result = self::get_connection()->update($sql);
        self::transaction_commit();
        return $result;
    }

    /**
     * The third approach, count using options, accepts an option hash as the only parameter.
     */
    public static function count($options = array()) {
        $c = self::first(array_merge($options, array('select' => 'count(*) rows')));
        return $c->rows;
    }

    /**
     * auto increament counter, default auto add 1 to $column_name.
     * if $step is set, then add $step to $column_name.
     */
    public static function counter($id, $column_name, $step = 1) {
        $model_name = self::get_model_name();
        if(isset(self::$cached_active_records[$model_name]) && isset(self::$cached_active_records[$model_name][$id])) {
            $object = self::$cached_active_records[$model_name][$id];
            $object->attributes[$column_name] = $object->attributes[$column_name] + $step;
            $object->save();
            return true;
        } else {
            $quoted_table_name = self::quoted_table_name($model_name);
            $quoted_primary_key = self::quoted_primary_key($model_name);
            $quoted_column_name = self::quote_column_name($column_name);
            $sql = "UPDATE " . $quoted_table_name . " SET " . $quoted_column_name . " = " . $quoted_column_name . " + " . $step . " WHERE " . $quoted_primary_key . " = " . $id;
            self::transaction_start();
            $result = self::get_connection()->update($sql);
            self::transaction_commit();
            return $result;
        }
    }

    public static function avg($column_name, $options = array()) {
        $quoted_column_name = self::quote_column_name($column_name);
        $avg = self::first(array_merge($options, array("select" => "AVG($quoted_column_name) AS average")));
        return $avg->average;
    }

    public static function min($column_name, $options = array()) {
        $quoted_column_name = self::quote_column_name($column_name);
        $min = self::first(array_merge($options, array("select" => "MIN($quoted_column_name) AS minimum")));
        return $min->minimum;
    }

    public static function max($column_name, $options = array()) {
        $quoted_column_name = self::quote_column_name($column_name);
        $max = self::first(array_merge($options, array("select" => "MAX($quoted_column_name) AS maximum")));
        return $max->maximum;
    }

    public static function sum($column_name, $options = array()) {
        $quoted_column_name = self::quote_column_name($column_name);
        $sum = self::first(array_merge($options, array("select" => "SUM($quoted_column_name) AS summary")));
        return $sum->summary;
    }

    /**
     * default $exclusive is false, remove object which does not match $conditions hash from $objects.
     * if $exclusive is true, then remove object which match $conditions hash from $objects.
     * return remain objects.
     */
    public static function filter($objects, $conditions, $exclusive = false) {
        $results = array();
        foreach($objects as $object) {
            $matched = true;
            foreach($conditions as $key => $value) {
                if($object->attributes[$key] !== $value) {
                    $matched = false;
                    break;
                }
            }
            if($matched) {
                $results[] = $object;
            }
        }
        if($exclusive) {
            return array_diff($objects, $results);
        } else {
            return $results;
        }
    }

    /**
     * if $all_models is true, will clear cache all, otherwise, only clear caches of target model.
     */
    public static function clear_cache() {
        self::$cached_active_records = array();
    }

    public static function clear_model_cache() {
        $model_name = self::get_model_name();
        self::$cached_active_records[$model_name] = array();
    }

    public static function get_table_name() {
        $model_name = self::get_model_name();
        return self::get_table_name_by_model($model_name);
    }

    public static function get_primary_key() {
        $model_name = self::get_model_name();
        return self::get_primary_key_by_model($model_name);
    }

    /**
     * Returns an array of columns for the table associated with this class.
     */
    public static function get_columns() {
        $model_name = self::get_model_name();
        return self::columns($model_name);
    }

    /**
     * Returns a string like 'ID:bigint(20) unsigned, post_author:bigint(20), post_date:datetime'.
     */
    public static function inspect() {
        $columns = self::get_columns();
        $desc = array();
        foreach($columns as $column) {
            $desc[] = $column[0] . ':' . $column[1];
        }
        return implode(', ', $desc);
    }

    /**
     * Transaction support for database operations.
     * If revoke this static method manually, should revoke static method
     * transaction_commit() or transation_rollback() manually to commit or rollback transactions.
     */
    public static function transaction_start() {
        if(self::$transaction_started === false) {
            self::$transaction_started = true;
            self::get_connection()->transaction_start();
            self::do_action('transaction_start');
        }
    }

    public static function transaction_commit() {
        if(self::$transaction_started) {
            self::$transaction_started = false;
            self::get_connection()->transaction_commit();
            self::do_action('transaction_commit');
        }
    }

    public static function transaction_rollback() {
        if(self::$transaction_started) {
            self::$transaction_started = false;
            self::get_connection()->transaction_rollback();
            self::do_action('transaction_rollback');
        }
    }

    // ======= ActiveRecord private static methods =======

    private static function quote_column_name($column_name) {
        return self::get_connection()->quote_column_name($column_name);
    }

    private static function quote_table_name($table_name) {
        return self::get_connection()->quote_table_name($table_name);
    }

    private static function quoted_primary_key($model_name) {
        return self::quote_column_name(self::get_primary_key_by_model($model_name));
    }

    private static function quoted_table_name($model_name) {
        return self::quote_table_name(self::get_table_name_by_model($model_name));
    }

    private static function quote_column_value($value) {
        if(is_numeric($value)) {
            return $value;
        }
        elseif(is_null($value)) {
            return 'NULL';
        } else {
            return "'" . self::escape_string($value) . "'";
        }
    }

    /**
     * prevent from sql injection.
     */
    private static function escape_string($value) {
        if(!is_string($value)) {
            return $value;
        } else {
            // php get_magic_quotes_gpc() function default return true.
            if(get_magic_quotes_gpc()) {
                $value = stripslashes($value);
            }
            // for mysqli
            if(function_exists('mysqli_real_escape_string')) {
                return mysqli_real_escape_string($value);
            }
            // for mysql
            if(function_exists('mysql_real_escape_string')) {
                return mysql_real_escape_string($value);
            }
            if(function_exists('pg_escape_string')) {
                return pg_escape_string($value);
            }
            return addslashes($value);
        }
    }

    /**
     * Used to sanitize objects before they're used in an SELECT SQL statement.
     */
    private static function quote_value_for_conditions($column, $value) {
        $column = self::quote_column_name($column);
        if(is_null($value)) {
            return $column . " IS NULL";
        }
        elseif(is_array($value)) {
            $value = array_unique($value);
            $size = count($value);
            if($size == 0) {
                throw new FireflyException("The second parameter can not be an empty array!");
            }
            elseif($size == 1) {
                return $column . " = " . self::quote_column_value($value[0]);
            } else {
                return $column . " IN ('" . implode("', '", $value) . "')";
            }
        } else {
            // is_string || is_numeric
            return $column . " = " . self::quote_column_value($value);
        }
    }

    /**
     * Accepts an array, hash or string of SQL conditions and sanitizes them into a valid SQL fragment for a WHERE clause.
     * array("name=%s and group_id=%s", "foo'bar", 4) returns  "name='foo''bar' and group_id='4'"
     * array("name"=>"foo'bar", "group_id"=>4) returns  "name='foo\'bar' and group_id='4'"
     * "name='foo''bar' and group_id='4'" returns "name='foo''bar' and group_id='4'"
     */
    private static function sanitize($object) {
        if(is_array($object)) {
            if(isset($object[0])) {
                // for array("name=%s and group_id=%d", "foo'bar", 4) and array("name=? and group_id=?", "foo'bar", 4)
                $object[0] = str_replace('?', '%s', $object[0]);
                $sets = array($object[0]);
                $len = count($object);
                for($i = 1; $i < $len; $i++) {
                    $sets[] = self::quote_column_value($object[$i]);
                }
                return call_user_func_array('sprintf', $sets);
            } else {
                // for array("name"=>"foo'bar", "group_id"=>4)
                $where = array();
                foreach($object as $key => $value) {
                    $where[] = self::quote_value_for_conditions($key, $value);
                }
                return implode(' AND ', $where);
            }
        } else {
            return $object;
        }
    }

    private static function sanitize_conditions($options) {
        return self::sanitize($options);
    }

    /**
     * quote value for UPDATE SQL.
     */
    private static function sanitize_assignment($updates) {
        $sets = array();
        foreach($updates as $key => $value) {
            $column = self::quote_column_name($key);
            $sets[] = $column . '=' . self::quote_column_value($value);
        }
        return implode(', ', $sets);
    }

    /**
     * Validate find method options.
     */
    private static function validate_find_options($ids, $options) {
        if(!is_string($ids) && !is_array($ids) && !is_numeric($ids)) {
            throw new FireflyException("First parameter of find method can not be parsed: " . var_export($ids, true));
        }
        if(!is_array($options)) {
            throw new FireflyException("Second parameter of find method expected be Array but " . var_export($options, true));
        }
    }

    /**
     * Validate database connection parameters.
     */
    private static function validate_connection_params($config) {
        if(empty($config['adapter'])) {
            throw new FireflyException("Unkown database adapter.");
        }
        if(empty($config['host'])) {
            throw new FireflyException("Unkown database host.");
        }
        if(empty($config['database'])) {
            throw new FireflyException("Unkown database name.");
        }
        if(!isset($config['username'])) {
            throw new FireflyException("Unkown database username.");
        }
        if(!isset($config['password'])) {
            throw new FireflyException("Unkown database password.");
        }
    }

    private static function cache_active_record_object($model_name, $id, $object) {
        self::$cached_active_records[$model_name][$id] = $object;
    }

    private static function construct_finder_sql($model_name, $options) {
        $table_name = self::quoted_table_name($model_name);
        $sql = "SELECT ";
        $sql .= self::add_select_fields($table_name, $options);
        $sql .= self::add_from_tables($table_name, $options);
        $sql .= self::add_joins($options);
        $sql .= self::add_conditions($options);
        $sql .= self::append_sql_options($options);
        return $sql;
    }

    private static function construct_finder_sql_with_associations($model_name, $options, $include_associations) {
        $quoted_table_name = self::quoted_table_name($model_name);
        $sql = self::add_select_and_joins_with_associations($model_name, $quoted_table_name, $options, $include_associations);
        // NOTICE: columns in select/where clause maybe ambiguous, should explicit those columns in options.
        $sql .= self::add_conditions_with_eager_load($model_name, $quoted_table_name, $options, $include_associations);
        $sql .= self::append_sql_options($options);
        return $sql;
    }

    private static function construct_update_sql($model_name, $updates, $options) {
        $table_name = self::quoted_table_name($model_name);
        $sets = self::sanitize_assignment($updates);
        $sql = "UPDATE " . $table_name . " SET " . $sets;
        $sql .= self::add_conditions($options);
        $sql .= self::add_order($options);
        $sql .= self::add_limit_without_offset($options);
        return $sql;
    }

    private static function construct_delete_sql($model_name, $options = array()) {
        $table_name = self::quoted_table_name($model_name);
        $sql = "DELETE FROM " . $table_name;
        $sql .= self::add_conditions($options);
        $sql .= self::add_order($options);
        $sql .= self::add_limit_without_offset($options);
        return $sql;
    }

    private static function find_first($model_name, $options = array()) {
        $options = array_merge($options, array('limit' => 1));
        $records = self::find_every($model_name, $options);
        if(!empty($records)) {
            return $records[0];
        } else {
            return false;
        }
    }

    private static function find_last($model_name, $options = array()) {
        if(isset($options['order'])) {
            $options['order'] = self::reverse_sql_order($options['order']);
        } else {
            $quoted_primary_key = self::quoted_primary_key($model_name);
            $options['order'] = "$quoted_primary_key DESC";
        }
        return self::find_first($model_name, $options);
    }

    private static function find_all($model_name, $options = array()) {
        return self::find_every($model_name, $options);
    }

    private static function find_from_ids($model_name, $ids, $options = array()) {
        if(is_array($ids)) {
            $ids = array_unique($ids);
            $size = count($ids);
            if($size == 0) {
                throw new FireflyException("Couldn't find Record without an id.");
            }
            elseif($size == 1) {
                $object = self::find_one($model_name, $ids[0], $options);
                if($object) {
                    return array($object);
                } else {
                    return array();
                }
            } else {
                return self::find_some($model_name, $ids, $options);
            }
        } else {
            return self::find_one($model_name, $ids, $options);
        }
    }

    private static function find_some($model_name, $ids, $options = array()) {
        $ids_str = "'" . implode("', '", $ids) . "'";
        $quoted_primary_key = self::quoted_primary_key($model_name);
        if(isset($options['joins']) || isset($options['include'])) {
            // prevent from ambiguous columns
            $quoted_table_name = self::quoted_table_name($model_name);
            $quoted_primary_key = $quoted_table_name . "." . $quoted_primary_key;
        }
        $ids_list = $quoted_primary_key . " IN (" . $ids_str . ")";
        if(isset($options['conditions'])) {
            $options['conditions'] = self::sanitize_conditions($options['conditions']) . " AND $ids_list";
        } else {
            $options['conditions'] = $ids_list;
        }
        return self::find_every($model_name, $options);
    }

    private static function find_one($model_name, $id, $options = array()) {
        if(is_null($id)) {
            return false;
        }
        $cached_object = self::find_object_from_caches($model_name, $id);
        if(!is_null($cached_object)) {
            return $cached_object;
        }
        $id = self::quote_column_value($id);
        $quoted_primary_key = self::quoted_primary_key($model_name);
        if(isset($options['joins']) || isset($options['include'])) {
            // prevent from ambiguous columns
            $quoted_table_name = self::quoted_table_name($model_name);
            $conditions = "$quoted_table_name.$quoted_primary_key = " . $id;
        } else {
            $conditions = "$quoted_primary_key = " . $id;
        }
        $options['conditions'] = $conditions;
        $objects = self::find_every($model_name, $options);
        if(!empty($objects)) {
            return $objects[0];
        } else {
            throw new FireflyException("Couldn't find record with id=" . $id);
        }
    }

    private static function find_every($model_name, $options) {
        $include_associations = self::with_eager_load_associations($model_name, $options);
        if($include_associations) {
            return self::find_with_eager_load_associations($model_name, $options, $include_associations);
        }
        $cache = self::get_cache_from_options($model_name, $options);
        $sql = self::construct_finder_sql($model_name, $options);
        $records = self::fetch_rows($sql);

        $active_record_objects = array();
        $primary_key = self::get_primary_key_by_model($model_name);
        foreach($records as $record) {
            $object = self::get_model_object($model_name, $primary_key, $record, $cache);
            if($cache) {
                // cache active record object with all properties found without select option.
                $id = $object->id();
                self::cache_active_record_object($model_name, $id, $object);
            }
            $active_record_objects[] = $object;
        }
        return $active_record_objects;
    }

    private static function find_object_from_caches($model_name, $id) {
        // first level cache, optimize performance of has_many and belongs_to query.
        if(!isset(self::$cached_active_records[$model_name])) {
            self::$cached_active_records[$model_name] = array();
        }
        if(isset(self::$cached_active_records[$model_name][$id])) {
            return self::$cached_active_records[$model_name][$id];
        }
        return null;
    }

    /**
     * delete model object from active record object caches.
     */
    private static function remove_objects_from_caches($model_name, $options) {
        $conditions = isset($options['conditions']) ? $options['conditions'] : array();
        if(isset(self::$cached_active_records[$model_name])) {
            self::$cached_active_records[$model_name] = self::filter(self::$cached_active_records[$model_name], $conditions, true);
        }
    }

    private static function find_with_eager_load_associations($model_name, $options, $include_associations) {
        $sql = self::construct_finder_sql_with_associations($model_name, $options, $include_associations);
        $records = self::fetch_rows($sql);
        return self::get_eager_load_objects($model_name, $records, $include_associations);
    }

    private static function get_readonly_attributes($model_name) {
        $props = self::get_model_static_properties($model_name);
        return array_merge($props['readonly_attributes'], array($props['primary_key']));
    }

    private static function get_configuration_file() {
        defined('DATABASE_CONFIG_FILE') ? null : define('DATABASE_CONFIG_FILE', FIREFLY_BASE_DIR . DS . 'config' . DS . 'database.php');
        return DATABASE_CONFIG_FILE;
    }

    /**
     * Hack for php, get ActiveRecord subclass name.
     */
    private static function get_model_name() {
        // php >= 5.3.0
        if (function_exists('get_called_class')) {
            return strtolower(get_called_class());
        }
        // foreach $trace to get static method revoking model name.
        $traces = debug_backtrace();
        foreach($traces as $key => $trace) {
            if($trace['file'] != __FILE__) {
                $lines = file($trace['file']);
                $line = $lines[$trace['line'] - 1];
                preg_match('/\w+(?=\s*::\s*\w+)/i', $line, $matches);
                if(empty($matches)) {
                    // for hacking php and get subclass name of active record.
                    throw new FireflyException("Please write code in one line style nearby line " . $trace['line']);
                } else {
                    // all $model_name using lower name in this program.
                    return strtolower($matches[0]);
                }
            }
        }
    }

    private static function get_model_static_properties($model_name) {
        if(!isset(self::$cached_static_properties_of_models[$model_name])) {
            $class = new ReflectionClass($model_name);
            self::$cached_static_properties_of_models[$model_name] = $class->getStaticProperties();
        }
        return self::$cached_static_properties_of_models[$model_name];
    }

    private static function get_table_name_by_model($model_name) {
        if(!isset(self::$cached_table_names[$model_name])) {
            $props = self::get_model_static_properties($model_name);
            if(is_null($props['table_name']) || $props['table_name'] == '') {
                $table_name = $model_name;
            } else {
                $table_name = $props['table_name'];
            }
            self::$cached_table_names[$model_name] = $props['table_name_prefix'] . $table_name . $props['table_name_suffix'];
        }
        return self::$cached_table_names[$model_name];
    }

    private static function get_primary_key_by_model($model_name) {
        $props = self::get_model_static_properties($model_name);
        return $props['primary_key'];
    }

    private static function get_ancestor_classes($class) {
        if(empty(self::$cached_model_ancestors[$class])) {
            $classes = array($class);
            while($class = get_parent_class($class)) {
                $classes[] = $class;
            }
            self::$cached_model_ancestors[$class] = $classes;
        }
        return self::$cached_model_ancestors[$class];
    }

    /**
     * only cache full properties of model object.
     */
    private static function get_cache_from_options($model_name, $options) {
        if(empty($options['select'])) {
            return true;
        }
        $table_name = self::get_table_name_by_model($model_name);
        $quoted_table_name = self::quote_table_name($table_name);
        if($options['select'] == $table_name . '.*' || $options['select'] == $quoted_table_name . '.*') {
            return true;
        }
        return false;
    }

    /**
     * map eager load resultset to associated model objects.
     */
    private static function get_eager_load_objects($model_name, $records, $include_associations) {
        $objects = array();
        $null_assoc_records = array();
        foreach($records as $record) {
            $table_index = 0;
            $object = self::get_model_object_by_record($model_name, $record, $table_index);
            $id = $object->id();
            $null_assoc_records[$id] = array('has_many' => array(), 'has_one' => array(), 'belongs_to' => array());
            foreach($include_associations as $assoc_type => $associations) {
                foreach ($associations as $association_id => $association) {
                    if(!isset($null_assoc_records[$id][$assoc_type][$association_id])) {
                        $null_assoc_records[$id][$assoc_type][$association_id] = false;
                    }
                    if($null_assoc_records[$id][$assoc_type][$association_id]) {
                        // for performance, preventing from creating null object.
                        continue;
                    }
                    $table_index++;
                    $assoc_model_name = self::get_association_model_name($assoc_type, $association_id, $association);
                    $assoc_quoted_primary_key = self::quoted_primary_key($assoc_model_name);
                    if($assoc_type == 'has_many') {
                        // has_many and many_to_many association object initialize.
                        if(!isset($object->attributes[$association_id])) {
                            $object->attributes[$association_id] = array();
                        }
                        $assoc_object =  self::get_model_object_by_record($assoc_model_name, $record, $table_index);
                        if($assoc_object) {
                            // avoid push array $object->attributes[$association_id] the same object repeatly using unique primary id.
                            $assoc_object_id = $assoc_object->id();
                            $object->attributes[$association_id][$assoc_object_id] = $assoc_object;
                            self::cache_active_record_object($assoc_model_name, $assoc_object_id, $assoc_object);
                        } else {
                            // begin match null record and ignore other rows for current $object.
                            $null_assoc_records[$id][$assoc_type][$association_id] = true;
                        }
                    } else {
                        // has_one and belongs_to associations.
                        if(!isset($object->attributes[$association_id])) {
                            $assoc_object =  self::get_model_object_by_record($assoc_model_name, $record, $table_index);
                            if($assoc_object) {
                                $assoc_object_id = $assoc_object->id();
                                // $object primary key should not be null.
                                $object->attributes[$association_id] = $assoc_object;
                                self::cache_active_record_object($assoc_model_name, $assoc_object_id, $assoc_object);
                            } else {
                                // begin match null record and ignore other rows for current $object.
                                $null_assoc_records[$id][$assoc_type][$association_id] = true;
                            }
                        }
                    }
                }
            }
            // update $object cache according to $object primary key.
            $objects[$id] = $object;
            self::cache_active_record_object($model_name, $id, $object);
        }
        foreach($include_associations['has_many'] as $association_id => $association) {
            foreach($objects as $object) {
                $object->attributes[$association_id] = array_values($object->attributes[$association_id]);
            }
        }
        return array_values($objects);
    }

    private static function get_model_objects($model_name, $records) {
        $active_record_objects = array();
        $primary_key = self::get_primary_key_by_model($model_name);
        foreach($records as $record) {
            $active_record_objects[] = self::get_model_object($model_name, $primary_key, $record);
        }
        return $active_record_objects;
    }

    private static function get_model_object($model_name, $primary_key, $attributes, $cache = false) {
        $object = null;
        if($cache) {
            $id = $attributes[$primary_key];
            $object = self::find_object_from_caches($model_name, $id);
        }
        if(is_null($object)) {
            // create this object if there is no such object in caches.
            $object = new $model_name($attributes);
            $object->new_active_record = false;
        }
        return $object;
    }

    private static function get_model_object_by_record($model_name, $record, $table_index) {
        $attributes = array();
        $columns = self::columns($model_name);
        foreach($columns as $index => $column) {
            $column_name = $column;
            $alias = 't' . $table_index . '_c' . $index;
            $attributes[$column_name] = $record[$alias];
        }
        $primary_key = self::get_primary_key_by_model($model_name);
        if($attributes[$primary_key]) {
            return self::get_model_object($model_name, $primary_key, $attributes, true);
        } else {
            return null;
        }
    }

    // ============================= Associations ==============================

    private static function get_associations($model_name) {
        if(isset(self::$cached_associations[$model_name])) {
            return self::$cached_associations[$model_name];
        }
        $associations = array('has_one' => array(), 'has_many' => array(), 'belongs_to' => array());
        $classes = self::get_ancestor_classes($model_name);
        // inherit associations from ancestor classes.
        for($index = sizeof($classes) - 1; $index >= 0; $index--) {
            $props = self::get_model_static_properties($classes[$index]);
            // convert associations array to hash.
            $props['has_one'] = is_string($props['has_one']) ? array($props['has_one'] => array()) : self::hashed_associations($props['has_one']);
            $props['has_many'] = is_string($props['has_many']) ? array($props['has_many'] => array()) : self::hashed_associations($props['has_many']);
            $props['belongs_to'] = is_string($props['belongs_to']) ? array($props['belongs_to'] => array()) : self::hashed_associations($props['belongs_to']);
            $associations['has_one'] = array_merge($associations['has_one'], $props['has_one']);
            $associations['has_many'] = array_merge($associations['has_many'], $props['has_many']);
            $associations['belongs_to'] = array_merge($associations['belongs_to'], $props['belongs_to']);
        }
        // cache and return hashed associations.
        self::$cached_associations[$model_name] = $associations;
        return $associations;
    }

    private static function get_association_keys($model_name) {
        if(isset(self::$cached_association_keys[$model_name])) {
            return self::$cached_association_keys[$model_name];
        }
        $keys = array();
        $associations = self::get_associations($model_name);
        foreach($associations as $association) {
            $keys = array_merge($keys, array_keys($association));
        }
        self::$cached_association_keys[$model_name] = $keys;
        return $keys;
    }

    private static function get_association_objects($object, $options) {
        // $options['include'] is string and value is association_id.
        $association_id = $options['include'];
        $model_name = $object->get_object_model_name();
        $associations = self::get_associations($model_name);
        if(in_array($association_id, array_keys($associations['has_one']))) {
            self::get_has_one_association_objects($object, $model_name, $association_id, $associations['has_one'][$association_id]);
        }
        elseif(in_array($association_id, array_keys($associations['has_many']))) {
            self::get_has_many_association_objects($object, $model_name, $association_id, $associations['has_many'][$association_id]);
        }
        elseif(in_array($association_id, array_keys($associations['belongs_to']))) {
            self::get_belongs_to_association_objects($object, $model_name, $association_id, $associations['belongs_to'][$association_id]);
        } else {
            throw new FireflyException("Unknown association: " . $association_id);
        }
    }

    private static function get_foreign_key($association_type, $model_name, $assoc_model_name, $associations) {
        if(isset($associations['foreign_key'])) {
            return $associations['foreign_key'];
        } else {
            $foreign_key = $model_name . '_id'; // for has_one or has_many.
            if($association_type == 'belongs_to') {
                $foreign_key = $assoc_model_name . '_id';
            }
            return strtolower($foreign_key);
        }
    }

    private static function get_association_model_name($association_type, $association_id, $options) {
        $class_name = $association_id; // has_one or belongs_to
        if(isset($options['class_name'])) {
            $class_name = $options['class_name'];
        } else {
            // other association must specify class_name, especially for belongs_to association.
            $class_name = $association_id;
        }
        // all $model_name using lower name in this ActiveRecord class.
        return strtolower($class_name);
    }

    /**
     * processing many_to_many table relationships, 'relation_table' key must be available. Example:
     * $has_many = array('search_engines' => array('through' => array('relation_table' => 'cleints_search_engines', 'column1' => 'client_id', 'column2' => 'search_engine_id'), 'class_name' => 'SearchEngine'));
     */
    private static function get_many_to_many_joins($association, $assoc_model_name) {
        if(isset($association['through']) && $through = $association['through']) {
            if(isset($through['relation_table']) && $relation_table = $through['relation_table']) {
                $quoted_relation_table = self::quote_table_name($relation_table);
                $quoted_table_name = self::quoted_table_name($assoc_model_name);
                $quoted_primary_key = self::quoted_primary_key($assoc_model_name);
                if(isset($through['column2']) && $foreign_key = $through['column2']) {
                    $quoted_foreign_key = self::quote_column_name($foreign_key);
                } else {
                    $foreign_key = $assoc_model_name . '_id';
                    $quoted_foreign_key = self::quote_column_name($foreign_key);
                }
                $joins = "LEFT OUTER JOIN $quoted_relation_table ON $quoted_relation_table.$quoted_foreign_key = $quoted_table_name.$quoted_primary_key";
                return $joins;
            } else {
                throw new FireflyException("Please set value to key 'relation_table'.");
            }
        } else {
            return '';
        }
    }

    /**
     * get association conditions from association options.
     */
    private static function get_association_conditions($association) {
        if(array_key_exists('conditions', $association)) {
            return $association['conditions'];
        } else {
            return array();
        }
    }

    /**
     * Specifies a one-to-one association with another class. This method should only be used
     * if this class contains the foreign key. If the other class contains the foreign key,
     * then you should use +has_one+ instead.
     *
     * === Options
     *
     * [class_name]
     *   Specify the class name of the association. Use it only if that name can't be inferred
     *   from the association name. So <tt>has_one = array('author')</tt> will by default be linked to the Author class, but
     *   if the real class name is Person, you'll have to specify it with this option.
     * [foreign_key]
     *   Specify the foreign key used for the association. By default this is guessed to be the name
     *   of the associated class with an "_id" suffix.
     */
    private static function get_belongs_to_association_objects($object, $model_name, $association_id, $belongs_to_assoc) {
        $assoc_model_name = self::get_association_model_name('belongs_to', $association_id, $belongs_to_assoc);
        $foreign_key = self::get_foreign_key('belongs_to', $model_name, $assoc_model_name, $belongs_to_assoc);
        $belongs_to_object = self::find_one($assoc_model_name, $object->attributes[$foreign_key]);
        $object->attributes[$association_id] = $belongs_to_object;
    }

    /**
     * Specifies a one-to-one association with another class. This method should only be used
     * if the other class contains the foreign key. If the current class contains the foreign key,
     * then you should use +belongs_to+ instead.
     *
     * The declaration can also include an options hash to specialize the behavior of the association.
     * === Options
     *
     * [class_name]
     *   Specify the class name of the association. Use it only if that name can't be inferred
     *   from the association name.
     * [conditions]
     *   Specify the conditions that the associated object must meet in order to be included as a +WHERE+
     *   SQL fragment, such as <tt>rank = 5</tt>.
     * [foreign_key]
     *   Specify the foreign key used for the association. By default this is guessed to be the name
     *   of this class in lower-case and "_id" suffixed. So a Person class that makes a +has_one+ association
     *   will use "person_id" as the default <tt>foreign_key</tt>.
     */
    private static function get_has_one_association_objects($object, $model_name, $association_id, $has_one_assoc) {
        $assoc_model_name = self::get_association_model_name('has_one', $association_id, $has_one_assoc);
        $foreign_key = self::get_foreign_key('has_one', $model_name, $assoc_model_name, $has_one_assoc);
        $conditions = array_merge(self::get_association_conditions($has_one_assoc), array($foreign_key => $object->id()));
        $has_one_object = self::find_first($assoc_model_name, array('conditions' => $conditions));
        $object->attributes[$association_id] = $has_one_object;
    }

    /**
     * === options
     *
     * [class_name]
     *   Specify the class name of the association. Use it only if that name can't be inferred
     *   from the association name. So <tt>has_many = array('products')</tt> will by default be linked to the Product class, but
     *   if the real class name is SpecialProduct, you'll have to specify it with this option.
     * [conditions]
     *   Specify the conditions that the associated objects must meet in order to be included as a +WHERE+
     *   SQL fragment, such as <tt>price > 5 AND name LIKE 'B%'</tt>.  Record creations from the association are scoped if a hash
     *   is used.
     * [foreign_key]
     *   Specify the foreign key used for the association. By default this is guessed to be the name
     *   of this class in lower-case and "_id" suffixed. So a Person class that makes a +has_many+ association will use "person_id"
     *   as the default <tt>foreign_key</tt>.
     * [through]
     *   Specifies a relation table and composite columns of this relation table through which to perform the query.
     *   [relation_table] is the name of relation table.
     *   [column1] is foreign key of current model, default value is model_name suffixed with '_id'.
     *   [column2] is foreign key of association model, default value is association_model_name suffixed with '_id'.
     */
    private static function get_has_many_association_objects($object, $model_name, $association_id, $has_many_assoc) {
        $assoc_model_name = self::get_association_model_name('has_many', $association_id, $has_many_assoc);
        $foreign_key = self::get_foreign_key('has_many', $model_name, $assoc_model_name, $has_many_assoc);
        $conditions = self::get_association_conditions($has_many_assoc);
        $joins = self::get_many_to_many_joins($has_many_assoc, $assoc_model_name);
        if($joins) {
            // many_to_many relationship between tables.
            $through = $has_many_assoc['through'];
            if(isset($through['column1']) && $through['column1']) {
                $foreign_key = $through['column1'];
            }
            $quoted_foreign_key = self::quote_column_name($foreign_key);
            $conditions = self::sanitize_conditions($conditions);
            if($conditions) {
                $conditions = "$conditions AND ";
            }
            $quoted_relation_table = self::quote_table_name($through['relation_table']);
            $conditions .= "$quoted_relation_table.$quoted_foreign_key = " . $object->id();
        } else {
            $conditions = array_merge($conditions, array($foreign_key => $object->id()));
        }
        $has_many_objects = self::find_all($assoc_model_name, array('joins' => $joins, 'conditions' => $conditions));
        $object->attributes[$association_id] = $has_many_objects;
    }

    private static function eager_load_included_associations($association_id, $association_type, $include, $options) {
        // parse find options for preload include related associations.
        if(!isset($options['limit']) || $association_type != 'has_many') {
            // if exists $options['limit'] and association type is has_many association
            // can not avoid 1+N sql problem, should use lazy load.
            if(is_array($include) && in_array($association_id, $include)) {
                return true;
            }
            if(is_string($include) && $association_id == $include) {
                return true;
            }
        }
        return false;
    }

    /**
     * if $model_name class has associations confiture
     * return associations according to $options['include'], else return false.
     */
    private static function with_eager_load_associations($model_name, $options) {
        if(!empty($options['include'])) {
            $includes = array();
            $eager_load = false;
            $include = $options['include'];
            $associations = self::get_associations($model_name);
            foreach($associations as $assoc_type => $association) {
                // $assoc_type is has_many/has_one/belongs_to
                $includes[$assoc_type] = array();
                foreach($association as $assoc_id => $assoc_options) {
                    if(self::eager_load_included_associations($assoc_id, $assoc_type, $include, $options)) {
                        $eager_load = true;
                        $includes[$assoc_type][$assoc_id] = $assoc_options;
                    }
                }
            }
            if($eager_load) {
                return $includes;
            }
        }
        return false;
    }

    private static function hashed_associations($associations) {
        $assoc = array();
        foreach($associations as $key => $value) {
            if(is_string($value)) {
                $assoc[$value] = array();
            } else {
                $assoc[$key] = $value;
            }
        }
        return $assoc;
    }

    private static function reverse_sql_order($order_query) {
        $orders = explode(',', $order_query);
        foreach($orders as $key => $order) {
            if(preg_match('/\s+asc\s*$/i', $order)) {
                $orders[$key] = preg_replace('/\s+asc\s*$/i', ' DESC', $order);
            }
            elseif(preg_match('/\s+desc\s*$/i', $order)) {
                $orders[$key] = preg_replace('/\s+desc\s*$/i', ' ASC', $order);
            } else {
                $orders[$key] .= " DESC";
            }
        }
        return implode(', ', $orders);
    }

    private static function add_select_fields($table_name, $options) {
        if(isset($options['select']) && $select = $options['select']) {
            return $select;
        } else {
            if(isset($options['joins'])) {
                return $table_name . ".*";
            } else {
                return "*";
            }
        }
    }

    private static function add_from_tables($table_name, $options) {
        $sql = " FROM ";
        if(isset($options['from']) && $from = $options['from']) {
            return $sql . $from;
        } else {
            return $sql . $table_name;
        }
    }

    private static function add_joins($options) {
        $joins = "";
        if(isset($options['joins']) && $joins = $options['joins']) {
            $joins = " " . $options['joins'];
        }
        return $joins;
    }

    /**
     * NOTICE: ignore 'joins' in $options when find with include associations.
     */
    private static function add_select_and_joins_with_associations($model_name, $quoted_table_name, $options, $include_associations) {
        $select = array();
        $joins = array();
        $table_index = 0;
        $quoted_primary_key = self::quoted_primary_key($model_name);
        array_push($select, self::get_table_columns_aliases($table_index, $model_name, $quoted_table_name));
        foreach($include_associations as $assoc_type => $associations) {
            foreach ($associations as $association_id => $association) {
                $assoc_model_name = self::get_association_model_name($assoc_type, $association_id, $association);
                $assoc_quoted_table_name = self::quoted_table_name($assoc_model_name);
                array_push($select, self::get_table_columns_aliases(++$table_index, $assoc_model_name, $assoc_quoted_table_name));
                $foreign_key = self::get_foreign_key($assoc_type, $model_name, $assoc_model_name, $association);
                $quoted_foreign_key = self::quote_column_name($foreign_key);
                if($assoc_type == 'belongs_to') {
                    $assoc_quoted_primary_key = self::quoted_primary_key($assoc_model_name);
                    array_push($joins, " LEFT OUTER JOIN $assoc_quoted_table_name ON $quoted_table_name.$quoted_foreign_key = $assoc_quoted_table_name.$assoc_quoted_primary_key");
                } else {
                    if(isset($association['through'])) {
                        // many_to_many association.
                        $through = $association['through'];
                        $assoc_quoted_primary_key = self::quoted_primary_key($assoc_model_name);
                        $relation_table = $through['relation_table'];
                        $quoted_relation_table = self::quote_table_name($relation_table);
                        if(isset($through['column1']) && $foreign_key = $through['column1']) {
                            $quoted_foreign_key = self::quote_column_name($foreign_key);
                        } else {
                            $foreign_key = $model_name . '_id';
                            $quoted_foreign_key = self::quote_column_name($foreign_key);
                        }
                        if(isset($through['column2']) && $assoc_foreign_key = $through['column2']) {
                            $assoc_quoted_foreign_key = self::quote_column_name($assoc_foreign_key);
                        } else {
                            $assoc_foreign_key = $assoc_model_name . '_id';
                            $assoc_quoted_foreign_key = self::quote_column_name($assoc_foreign_key);
                        }
                        array_push($joins, " LEFT OUTER JOIN $quoted_relation_table ON $quoted_relation_table.$quoted_foreign_key = $quoted_table_name.$quoted_primary_key");
                        array_push($joins, " LEFT OUTER JOIN $assoc_quoted_table_name ON $quoted_relation_table.$assoc_quoted_foreign_key = $assoc_quoted_table_name.$assoc_quoted_primary_key");
                    } else {
                        array_push($joins, " LEFT OUTER JOIN $assoc_quoted_table_name ON $quoted_table_name.$quoted_primary_key = $assoc_quoted_table_name.$quoted_foreign_key");
                    }
                }
            }
        }
        $sql = "SELECT ";
        $sql .= join(', ', $select);
        $sql .= " FROM " . $quoted_table_name;
        $sql .= join('', $joins);
        return $sql;
    }

    private static function get_table_columns_aliases($table_index, $model_name, $quoted_table_name) {
        $aliases = array();
        $columns = self::columns($model_name);
        foreach($columns as $index => $column) {
            $column = $columns[$index];
            $quoted_column = self::quote_column_name($column);
            $alias = $quoted_table_name . "." . $quoted_column . " AS t" . $table_index . "_c" . $index;
            array_push($aliases, $alias);
        }
        return join(', ', $aliases);
    }

    private static function append_sql_options($options) {
        $sql = "";
        $sql .= self::add_group($options);
        $sql .= self::add_order($options);
        $sql .= self::add_limit($options);
        $sql .= self::add_lock($options);
        return $sql;
    }

    private static function add_conditions_with_eager_load($model_name, $quoted_table_name, $options, $include_associations) {
        $conditions = "";
        if(isset($options['conditions']) && $options['conditions']) {
            $conditions .= " WHERE " . self::sanitize_conditions($options['conditions']);
        }
        foreach($include_associations as $assoc_type => $associations) {
            foreach ($associations as $association_id => $association) {
                if(isset($association['conditions']) && $association['conditions']) {
                    if($conditions == "") {
                        $conditions .= " WHERE " . self::sanitize_conditions($association['conditions']);
                    } else {
                        $conditions .= " AND " . self::sanitize_conditions($association['conditions']);
                    }
                }
            }
        }
        return $conditions;
    }

    /**
     * Adds a sanitized version of +conditions+ to the +sql+ string.
     */
    private static function add_conditions($options) {
        $conditions = "";
        if(isset($options['conditions']) && $options['conditions']) {
            $conditions .= " WHERE " . self::sanitize_conditions($options['conditions']);
        }
        return $conditions;
    }

    private static function add_order($options) {
        $order = "";
        if(isset($options['order']) && $order = $options['order']) {
            $order = " ORDER BY $order";
        }
        return $order;
    }

    private static function add_group($options) {
        $group = "";
        if(isset($options['group']) && $group = $options['group']) {
            $group = " GROUP BY $group";
            if(isset($options['having']) && $having = $options['having']) {
                $group .= " HAVING $having";
            }
        }
        return $group;
    }

    private static function add_limit($options) {
        $limit = "";
        if(isset($options['limit']) && $options['limit']) {
            $limit = " LIMIT ";
            if(isset($options['offset']) && $offset = $options['offset']) {
                $limit .= $offset . ", ";
            }
            $limit .= $options['limit'];
        }
        return $limit;
    }

    /**
     * not implement optimistic locking
     * because of different database connection don't share the same first level cache of active record object.
     */
    private static function add_lock($options) {
        $lock = "";
        if(isset($options['lock']) && $lock = $options['lock']) {
            if($lock === true) {
                return " FOR UPDATE";
            }
            if(is_string($lock)) {
                // such as "LOCK IN SHARE MODE" in mysql.
                return " " . $lock;
            }
        }
        return $lock;
    }

    private static function add_limit_without_offset($options) {
        if(isset($options['limit']) && $options['limit']) {
            return " LIMIT " . $options['limit'];
        }
        return "";
    }

    /**
     * Include config/database.php file, and return config array.
     */
    private static function load_configurations() {
        if(empty(self::$configurations)) {
            $config_file = self::get_configuration_file();
            $config = array();
            require($config_file);
            self::$configurations = $config[ENVIRONMENT];
        }
        return self::$configurations;
    }

    private static function columns($model_name) {
        if(empty(self::$cached_table_columns[$model_name])) {
            $table_name = self::get_table_name_by_model($model_name);
            $fields = array();
            $columns = self::get_connection()->columns($table_name);
            foreach($columns as $column) {
                array_push($fields, $column[0]);
            }
            self::$cached_table_columns[$model_name] = $fields;
        }
        return self::$cached_table_columns[$model_name];
    }

    private static function do_action($hook, $parameters = '') {
        if (function_exists('do_action')) {
            do_action($hook, $parameters);
        }
    }

    /**
     * fetcb rows by sql
     */
    private static function fetch_rows($sql) {
        return self::get_connection()->fetch_rows($sql);
    }

    // ======= ActiveRecord public instance methods =======

    private $new_active_record = true;
    private $lower_class_name = false;
    private $session_active_record = false;
    private $active_record_errors = array();

    public $attributes;

    /**
     * New objects can be instantiated as either empty (pass no construction parameter) or pre-set with
     * attributes but not yet saved (pass a hash with key names matching the associated table column names).
     * In both instances, valid attribute keys are determined by the column names of the associated table --
     * hence you can't have attributes that aren't part of the table columns.
     */
    public function __construct($attributes = null) {
        $this->attributes = $attributes ? $attributes : array();
        $this->after_initialize();
        $this->do_actions('after_initialize');
    }

    /**
     * Returns a clone of the record that hasn't been assigned an id yet and is treated as a new record.
     * Note that this is a "shallow" clone: it copies the object's attributes only, not its associations.
     * The extent of a "deep" clone is application-specific and is therefore left to the application to implement according to its need.
     */
    public function __clone() {
        // set id = null;
        $id = self::get_primary_key_by_model($this->get_object_model_name());
        $this->attributes[$id] = null;
    }

    /**
     * A model instance's primary key is always available as model.id
     * whether you name it the default 'id' or set it to something else.
     */
    public function id() {
        $attr_name = self::get_primary_key_by_model($this->get_object_model_name());
        $column = $this->attributes[$attr_name];
        return $column;
    }

    /**
     * Saves the model.
     *
     * If the model is new a record gets created in the database, otherwise the existing record gets updated.
     *
     * There's a series of callbacks associated with +save+.
     * If any of the <tt>before_*</tt> callbacks return +false+ the action is cancelled and +save+ returns +false+.
     *
     */
    public function save($validate = true) {
        return $this->create_or_update($validate);
    }

    /**
     * Deletes the record in the database.
     * This method will revoke callbacks such as before_destroy and after_destroy.
     */
    public function destroy() {
        self::transaction_start();
        $result = $this->delete_with_callbacks();
        if($result === false) {
            self::transaction_rollback();
        } else {
            self::transaction_commit();
        }
        return $result;
    }

    /**
     * Returns an instance of the specified +klass+ with the attributes of the current record.
     * This is mostly useful in relation to single-table inheritance structures where you want a subclass to appear as the superclass.
     */
    public function becomes($klass) {
        $attrs = $this->attributes;
        // set id = null;
        $attrs[self::get_primary_key_by_model($this->get_object_model_name())] = null;
        $new_klass_obj = new $klass($attrs);
        return $new_klass_obj;
    }

    /**
     * Updates a single attribute and saves the record without going through the normal validation procedure.
     */
    public function update_attribute($name, $value) {
        $model_name = $this->get_object_model_name();
        $readonly_attributes = self::get_readonly_attributes($model_name);
        if(in_array($name, $readonly_attributes)) {
            // it should throw exception when update readonly attribute.
            throw new FireflyException("'$name' is a readonly attribute, cannot be updated!");
        }
        $this->attributes[$name] = $value;
        $this->save();
    }

    /**
     * Updates all the attributes from the passed-in Hash and saves the record.
     * If the object is invalid, the saving will fail and false will be returned.
     */
    public function update_attributes($attributes) {
        // remove readonly attributes firstly.
        $this->attributes = array_merge($this->attributes, $this->remove_readonly_attributes($attributes));
        return $this->save();
    }

    /**
     * Reloads the attributes of this object from the database.
     */
    public function reload() {
        $model_name = $this->get_object_model_name();
        $id = $this->id();
        self::$cached_active_records[$model_name][$id] = null;
        $obj = self::find_one($model_name, $id);
        $this->attributes = $obj->attributes;
    }

    /**
     * Returns an array of names for the attributes available on this object sorted alphabetically.
     */
    public function attribute_names() {
        return array_keys($this->attributes);
    }

    public function is_new_object() {
        return $this->new_active_record;
    }

    public function is_session_object() {
        if (func_num_args() == 1) {
            $this->session_active_record = func_get_arg(0);
        }
        return $this->session_active_record;
    }

    // =============================== Errors ===============================

    /**
     * Adds an error message ($message) to the ($attribute), which will be returned on a call to $this->get_errors_on($attribute).
     */
    public function add_error($attribute, $message = 'invalid') {
        $this->active_record_errors[$attribute][] = $message;
    }

    public function clear_errors() {
        $this->active_record_errors = array();
    }

    public function has_errors() {
        return !empty($this->active_record_errors);
    }

    /**
     * Returns false, if no errors are associated with the specified $attribute.
     * Returns the error message, if one error is associated with the specified $attribute.
     * Returns an array of error messages, if more than one error is associated with the specified $attribute.
     */
    public function get_errors_on($attribute) {
        if(empty($this->active_record_errors[$attribute])) {
            return false;
        }
        elseif(count($this->active_record_errors[$attribute]) == 1) {
            return $this->active_record_errors[$attribute][0];
        } else {
            return $this->active_record_errors[$attribute];
        }
    }

    //  =============================== Callbacks ===============================
    /**
    * Callbacks are hooks into the life-cycle of an Active Record object that allows you to trigger logic
    * before or after an alteration of the object state. This can be used to make sure that associated and
    * dependent objects are deleted when destroy is called (by overwriting before_destroy) or to massage attributes
    * before they're validated (by overwriting before_validation). As an example of the callbacks initiated, consider
    * the ActiveRecord->save() call:
    *
    * - (-) save()
    * - (1) before_validation()
    * - (2) before_validation_on_create() / before_validation_on_update()
    * - (-) validate()
    * - (4) after_validation()
    * - (5) after_validation_on_create() / after_validation_on_update()
    * - (6) before_save()
    * - (7) before_create() / before_update()
    * - (-) create()
    * - (8) after_create() / after_update()
    * - (9) after_save()
    * - (10) after_destroy()
    * - (11) before_destroy()
    * - (12) after_initialize()
    *
    * That's a total of 17 callbacks, which gives you immense power to react and prepare for each state in the Active Record lifecycle.
    *
    * Examples:
    *   class CreditCard extends ActiveRecord {
    *       // Strip everything but digits, so the user can specify "555 234 34" or "5552-3434" or both will mean "55523434"
    *       function before_validation_on_create {
    *           if(!empty($this->number)){
    *               $this->number = ereg_replace('[^0-9]*','',$this->number);
    *           }
    *       }
    *   }
    *
    *   class Subscription extends ActiveRecord {
    *       var $before_create  = 'recordSignup';
    *
    *       function recordSignup() {
    *         $this->signed_up_on = date("Y-m-d");
    *       }
    *   }
    *
    * == Canceling callbacks ==
    *
    * If a before* callback returns false, all the later callbacks and the associated action are cancelled. If an after* callback returns
    * false, all the later callbacks are cancelled. Callbacks are generally run in the order they are defined.
    *
    * Override this methods to hook Active Records
    *
    */
    public function before_validation() {
        return true;
    }

    public function validate() {
        return true;
    }

    /**
     * The after_initialize callback will be called whenever an Active Record object is instantiated,
     * either by direcly using new or when a record is loaded from the database.
     */
    public function after_initialize() {
        return true;
    }

    public function validate_on_create() {
        return true;
    }

    public function validate_on_update() {
        return true;
    }

    public function before_validation_on_create() {
        return true;
    }

    public function before_validation_on_update() {
        return true;
    }

    public function before_save() {
        return true;
    }

    public function after_save() {
        return true;
    }

    public function before_update() {
        return true;
    }

    public function after_update() {
        return true;
    }

    public function after_validation() {
        return true;
    }

    public function after_validation_on_create() {
        return true;
    }

    public function after_validation_on_update() {
        return true;
    }

    public function before_create() {
        return true;
    }

    public function after_create() {
        return true;
    }

    public function after_destroy() {
        return true;
    }

    public function before_destroy() {
        return true;
    }

    // ======= ActiveRecord private instance methods =======

    private function get_object_model_name() {
        if ($this->lower_class_name == false) {
            $this->lower_class_name = strtolower(get_class($this));
        }
        return $this->lower_class_name;
    }

    private function create_or_update($validate) {
        if($validate && !$this->is_valid()) {
            return false;
        }
        return $this->save_with_callbacks();
    }

    private function save_with_callbacks() {
        $success = true;
        self::transaction_start();
        if($this->before_save()) {
            $this->do_actions('before_save');
            $result = $this->new_active_record ? $this->create_with_callbacks() : $this->update_with_callbacks();
            if($result === false) {
                // if result is false, create or update failure, should rollback transaction.
                $success = false;
            } else {
                if($this->after_save()) {
                    $this->do_actions('after_save');
                } else {
                    $success = false;
                }
            }
        } else {
            $success = false;
        }
        if($success === false) {
            self::transaction_rollback();
            $this->do_actions('after_rollback');
        } else {
            self::transaction_commit();
            $this->do_actions('after_commit');
        }

        return $success;
    }

    /**
     * Creates a record with values matching those of the instance attributes and returns its id.
     */
    private function create_with_callbacks() {
        if($this->before_create()) {
            $this->do_actions('before_create');
            if(!$id = $this->get_last_insert_id()) {
                return false;
            }
            if($this->after_create()) {
                $this->do_actions('after_create');
            } else {
                Logger::warn("Callback after_create failed, but record has been created!");
                return false;
            }
            $this->new_active_record = false;
            $this->id = $id;
            return $this;
        } else {
            return false;
        }
    }

    /**
     * Updates the associated record with values matching those of the instance attributes.
     * Returns the number of affected rows.
     */
    private function update_with_callbacks() {
        if($this->before_update()) {
            $this->do_actions('before_update');
            $table_columns = $this->attributes_from_column_definition($this->attributes);
            $safe_attributes = $this->remove_readonly_attributes($table_columns);
            $quoted_str = $this->quoted_column_values($safe_attributes);
            $model_name = $this->get_object_model_name();
            $table_name = self::quoted_table_name($model_name);
            $primary_key = self::quoted_primary_key($model_name);
            if($quoted_str == '') {
                return true;
            } else {
                $sql = "UPDATE $table_name SET $quoted_str WHERE $primary_key=" . self::quote_column_value($this->id());
                self::get_connection()->update($sql);
                if($this->after_update()) {
                    $this->do_actions('after_update');
                } else {
                    Logger::warn("Callback after_update failed, but record has been updated!");
                    return false;
                }
                return $this;
            }
        } else {
            return false;
        }
    }

    private function delete_with_callbacks() {
        $model_name = $this->get_object_model_name();
        $primary_key = self::get_primary_key_by_model($model_name);
        $options = array('conditions' => array($primary_key => $this->id()));
        $sql = self::construct_delete_sql($model_name, $options);
        if($this->before_destroy()) {
            $this->do_actions('before_destroy');
            self::get_connection()->delete($sql);
            if($this->after_destroy()) {
                $this->do_actions('after_destroy');
            } else {
                Logger::warn("Callback after_destroy failed, but record has been deleted!");
                return false;
            }
            self::remove_objects_from_caches($model_name, $options);
            return true;
        } else {
            return false;
        }
    }

    /**
     * Returns true if no errors were added otherwise false.
     */
    private function is_valid() {
        $this->clear_errors();
        if($this->before_validation() && $this->do_actions('before_validation')) {
            $this->validate();
            $this->after_validation();
            $this->do_actions('after_validation');

            if($this->is_new_object()) {
                if($this->before_validation_on_create()) {
                    $this->do_actions('before_validation_on_create');
                    $this->validate_on_create();
                    $this->after_validation_on_create();
                    $this->do_actions('after_validation_on_create');
                }
            } else {
                if($this->before_validation_on_update()) {
                    $this->do_actions('before_validation_on_update');
                    $this->validate_on_update();
                    $this->after_validation_on_update();
                    $this->do_actions('after_validation_on_update');
                }
            }
        }

        return !$this->has_errors();
    }

    private function get_last_insert_id() {
        $table_columns = $this->attributes_from_column_definition($this->attributes);
        $safe_attributes = $this->remove_readonly_attributes($table_columns);
        $quoted_attrs = $this->quotes_attributes($safe_attributes);
        $table_name = self::quoted_table_name($this->get_object_model_name());
        if($sequence = self::$sequence_class_name && class_exists($sequence)) {
            // create primary id by other class ($sequence_class_name class must implement public function get_next_primary_id),
            // because primary id may not auto increment.
            $seq = new $sequence;
            $id = $seq->get_next_primary_id();
            $model_name = $this->get_object_model_name();
            $primary_key = self::get_primary_key_by_model($model_name);
            $pk = self::quote_column_name($primary_key);
            $quoted_attrs[$pk] = self::quote_column_value($id);
        }
        if(empty($quoted_attrs)) {
            $id = self::get_connection()->empty_insert_statement($table_name);
        } else {
            $columns = implode(', ', array_keys($quoted_attrs));
            $values = implode(', ', array_values($quoted_attrs));
            $sql = "INSERT INTO " . $table_name . " (" . $columns . ") VALUES (" . $values . ")";
            $id = self::get_connection()->create($sql);
        }
        return $id;
    }

    /**
     * Calls the $method using the reference to each registered actions.
     */
    private function do_actions($method) {
        if (function_exists('do_action')) {
            do_action($this->get_object_model_name() . '.' . $method, $this);
        }
        return true;
    }

    /**
     * Removes attributes which have been marked as readonly.
     */
    private function remove_readonly_attributes($attributes) {
        $class_name = $this->get_object_model_name();
        $attr = array();
        foreach($attributes as $key => $value) {
            // filter attributes remain safe attributes, and remove default attributes.
            if(!in_array($key, self::get_readonly_attributes($class_name))) {
                $attr[$key] = $value;
            }
        }
        return $attr;
    }

    /**
     * Returns a copy of the attributes hash where all the values have been safely quoted for use in an SQL statement.
     * For create statement.
     */
    private function quotes_attributes($attributes) {
        $quoted_attrs = array();
        foreach($attributes as $key => $value) {
            $column = self::quote_column_name($key);
            $quoted_attrs[$column] = self::quote_column_value($value);
        }
        return $quoted_attrs;
    }

    /**
     * For update statement.
     */
    private function quoted_column_values($attributes) {
        return self::sanitize_assignment($attributes);
    }

    private function attributes_from_column_definition($attributes) {
        $model_name = $this->get_object_model_name();
        $columns = self::columns($model_name);
        $attrs = array();
        $keys = array_keys($attributes);
        foreach($columns as $column_name) {
            if(in_array($column_name, $keys)) {
                $attrs[$column_name] = $attributes[$column_name];
            } else {
                if(in_array($column_name, array('created_on', 'created_at')) && $this->new_active_record) {
                    $attrs[$column_name] = $this->set_record_timestamps($column_name);
                }
                if(in_array($column_name, array('updated_on', 'updated_at'))) {
                    $attrs[$column_name] = $this->set_record_timestamps($column_name);
                }
            }
        }
        return $attrs;
    }

    private function set_record_timestamps($column_name) {
        $time = time();
        $date = date('Y-m-d');
        if($column_name == 'created_at' || $column_name == 'updated_at') {
            return $date;
        }
        // created_on or updated_on.
        return $time;
    }

    public function __set($key, $value) {
        $this->attributes[$key] = $value;
    }

    public function __get($key) {
        if(!$this->session_active_record && array_key_exists($key, $this->attributes)) {
            return $this->attributes[$key];
        } else {
            $model_name = $this->get_object_model_name();
            if(in_array($key, self::get_association_keys($model_name))) {
                // for has_many, has_one and belongs_to properties.
                self::get_association_objects($this, array('include' => $key));
                return array_key_exists($key, $this->attributes) ? $this->attributes[$key] : null;
            } else {
                // for table columns which value is null.
                $columns = self::columns($model_name);
                if(in_array($key, $columns)) {
                    return array_key_exists($key, $this->attributes) ? $this->attributes[$key] : null;
                }
            }
            throw new FireflyException("Undefined activerecord attribute: " . $key);
        }
    }

    /**
     * __callStatic() PHP 5.3.0.
     */
    public function __call($method, $args) {
        throw new FireflyException("Unkown method has been called on ActiveRecord Object: " . $method);
    }

    public function __toString() {
        return var_export($this->attributes, true);
    }

}
?>
