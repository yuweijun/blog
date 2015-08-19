<?php

/**
 *
And don't miss the table dump. ^^

CREATE TABLE IF NOT EXISTS `sessions` (
  `session` varchar(255) character set utf8 collate utf8_bin NOT NULL,
  `session_expires` int(10) unsigned NOT NULL default '0',
  `session_data` text collate utf8_unicode_ci,
  PRIMARY KEY  (`session`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
 */
class Session {
    /**
     * a database connection resource
     * @var resource
     */
    private static $mysqli;

    /**
     * Open the session
     * @return bool
     */
    public static function open() {
        self::$mysqli = new mysqli('localhost', 'user', 'password', 'sessions', 3306) or die("connection failure!");
        return true;
    }

    /**
     * Close the session
     * @return bool
     */
    public static function close() {
        return self::$mysqli->close();
    }

    /**
     * Read the session
     * @param int session id
     * @return string string of the sessoin
     */
    public static function read($id) {
        $id = self::$mysqli->real_escape_string($id);
        $sql = sprintf("SELECT `session_data` FROM `sessions` WHERE `session` = '%s'", $id);
        if($result = self::$mysqli->query($sql)) {
            if($result->num_rows()) {
                $record = $result->fetch_assoc();
                return $record['session_data'];
            }
        }
        return '';
    }

    /**
     * Write the session
     * @param int session id
     * @param string data of the session
     */
    public static function write($id, $data) {
        $sql = sprintf("REPLACE INTO `sessions` VALUES('%s', '%s', '%s')", self::$mysqli->real_escape_string($id), time(), $data);
        return self::$mysqli->query($sql);
    }

    /**
     * Destoroy the session
     * @param int session id
     * @return bool
     */
    public static function destroy($id) {
        $sql = sprintf("DELETE FROM `sessions` WHERE `session` = '%s'", $id);
        return self::$mysqli->query($sql);
    }

    /**
     * Garbage Collector
     * @param int life time (sec.)
     * @return bool
     * @see session.gc_divisor      100
     * @see session.gc_maxlifetime 1440
     * @see session.gc_probability    1
     * @usage execution rate 1/100
     *        (session.gc_probability/session.gc_divisor)
     */
    public static function gc($max) {
        $sql = sprintf("DELETE FROM `sessions` WHERE `session_expires` < '%s'", (time() - $max));
        return self::$mysqli->query($sql);
    }
}

//ini_set('session.gc_probability', 50);
ini_set('session.save_handler', 'user');

session_set_save_handler(array('Session', 'open'), array('Session', 'close'), array('Session', 'read'), array('Session', 'write'), array('Session', 'destroy'), array('Session', 'gc'));

if(session_id() == "") {
    session_start();
}
//session_regenerate_id(false); //also works fine
if(isset($_SESSION['counter'])) {
    $_SESSION['counter']++;
} else {
    $_SESSION['counter'] = 1;
}
echo '<br/>SessionID: ' . session_id() . '<br/>Counter: ' . $_SESSION['counter'];
?>
