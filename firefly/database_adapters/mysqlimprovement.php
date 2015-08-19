<?php
include_once('interface_adapter.php');

class MysqlImprovement implements DatabaseAdapter {

  private static $instance = null;
  private static $mysqli = null;

  private function __construct() {
  }

  public static function establish_connection($host, $username, $password, $database, $port, $encoding = 'utf8') {
    if(self::$instance == null) {
      self::$instance = new MysqlImprovement();
      self::$mysqli = new mysqli($host, $username, $password, $database, $port) or self::$instance->error("Please check access privileges for '$username'@'$host' on '$database'.");
      self::$mysqli->query("SET NAMES $encoding");
      self::$mysqli->query("SET SQL_AUTO_IS_NULL=0");
      if (function_exists('add_action')) {
        // log mysql query sql and time costs.
        add_action('mysql.query.begin', array('Profiler', 'start'));
        add_action('mysql.query.end', array('Profiler', 'end'));
      }
    }
    return self::$instance;
  }

  public function execute($sql) {
    if (function_exists('do_action')) {
      do_action('mysql.query.begin', $sql);
      $boolean = $this->query($sql) or $this->error(self::$mysqli->error());
      do_action('mysql.query.end', $sql);
    } else {
      $boolean = $this->query($sql) or $this->error(self::$mysqli->error());
    }
    return $boolean;
  }

  public function fetch_rows($sql) {
    $results = $this->execute($sql);
    $rows = array();
    while($row = $results->fetch_assoc()) {
      $rows[] = $row;
    }
    return $rows;
  }

  public function create($sql) {
    $result = $this->execute($sql);
    return $result->insert_id;
  }

  public function update($sql) {
    $result = $this->execute($sql);
    return self::$mysqli->affected_rows();
  }

  public function delete($sql) {
    $result = $this->execute($sql);
    return self::$mysqli->affected_rows();
  }

  public function empty_insert_statement($table_name) {
    $sql = "INSERT INTO " . $table_name . " VALUES()";
    return $this->create($sql);
  }

  public function quote_column_name($name) {
    return "`$name`";
  }

  public function quote_table_name($name) {
    return preg_replace('/\./', '`.`', $this->quote_column_name($name));
  }

  public function columns($table_name) {
    $columns = array();
    $sql = "SHOW FIELDS FROM {$this->quote_table_name($table_name)}";
    $rows = $this->fetch_rows($sql);
    foreach($rows as $row) {
      $columns[] = array($row['Field'], $row['Type'], $row['Default'], $row['Null'] == 'YES');
    }
    return $columns;
  }

  public function transaction_start() {
    $this->execute('START TRANSACTION');
  }

  public function transaction_commit() {
    $this->execute('COMMIT');
  }

  public function transaction_rollback() {
    $this->execute('ROLLBACK');
  }

  public function __clone() {
    throw new FireflyException('clone is not allowed.');
  }

  private function query($sql) {
    return self::$mysqli->query($sql);
  }

  private function error($message) {
    throw new FireflyException($message);
  }

}
?>
