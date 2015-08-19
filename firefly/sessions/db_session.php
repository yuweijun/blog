<?php
include_once('interface_session.php');

class DBSession implements InterfaceSession {
  // create table for session.
  // CREATE TABLE `ws_sessions` ( `session_id` varchar(255) binary NOT NULL default '', `session_expires` int(10) unsigned NOT NULL default '0', `session_data` text, PRIMARY KEY  (`session_id`) ) TYPE=InnoDB;

  private $mysqli;
  private $life_time;

  public function __construct() {
    $this->life_time = get_cfg_var("session.gc_maxlife_time");
    session_set_save_handler(array(& $this, 'open'), array(& $this, 'close'), array(& $this, 'read'), array(& $this, 'write'), array(& $this, 'destroy'), array(& $this, 'gc'));
    register_shutdown_function('session_write_close');
    session_start();
  }

  public function open($save_path, $sess_name) {
    // get session-life_time
    // open database-connection
    $this->mysqli = new mysqli("server", "user", "password", "sessions", 3306) or die("mysqli connection failure");
    return true;
  }

  public function close() {
    $this->gc(ini_get('session.gc_maxlife_time'));
    // close database-connection
    return $this->mysqli->close();
  }

  public function read($sess_id) {
    // fetch session-data
    $res = $this->mysqli->query("SELECT session_data AS d FROM ws_sessions WHERE session_id = '$sess_id' AND session_expires > " . time());
    // return data or an empty string at failure
    if($row = $res->fetch_assoc())
      return $row['d'];
    return "";
  }

  public function write($sess_id, $sess_data) {
    // new session-expire-time
    $new_exp = time() + $this->life_time;
    // is a session with this id in the database?
    $res = $this->mysqli->query("SELECT * FROM ws_sessions WHERE session_id = '$sess_id'"); // if yes,
    if($res->num_rows()) {
      // ...update session-data
      $this->mysqli->query("UPDATE ws_sessions SET session_expires = '$new_exp', session_data = '$sess_data' WHERE session_id = '$sess_id'");
      // if something happened, return true
      if($this->mysqli->affected_rows())
        return true;
    }
    // if no session-data was found,
    else {
      // create a new row
      $this->mysqli->query("INSERT INTO ws_sessions ( session_id, session_expires, session_data) VALUES( '$sess_id', '$new_exp', '$sess_data')");
      // if row was created, return true
      if($this->mysqli->affected_rows())
        return true;
    }
    // an unknown error occured
    return false;
  }

  public function destroy($sess_id) {
    // delete session-data
    $this->mysqli->query("DELETE FROM ws_sessions WHERE session_id = '$sess_id'");
    // if session was deleted, return true,
    if($this->mysqli->affected_rows()) {
      return true;
    }
    return false;
  }

  public function gc($sess_max_life_time) {
    // delete old sessions
    $this->mysqli->query("DELETE FROM ws_sessions WHERE session_expires < " . time());
    // return affected rows
    return $this->mysqli->affected_rows();
  }
}
?>