<?php

class SPF_Database_MySQL {

    static private $SQL;
    static private $CONNECT;
    static private $LOG;
    static private $_isConnect;
    static public $NUM_QUERY = 0;
    static public $TIME_QUERY = 0.0;
    static public $TMP = array();

    static public function Connect() {
        self::$CONNECT = mysql_pconnect(SPF_DATABASE_HOST, SPF_DATABASE_USER, SPF_DATABASE_PASSWORD);
        if (mysql_select_db(SPF_DATABASE_DATABASE, self::$CONNECT)) {
            self::$_isConnect = true;
            self::setNames();
        }
        self::$LOG = true;
    }

    static public function setNames() {
        if (!self::$_isConnect) {
            self::Connect();
        }
        mysql_query("SET CHARACTER SET UTF8", self::$CONNECT);
        mysql_query("SET NAMES 'utf8'", self::$CONNECT);
    }

    static public function Query($query) {
        if (!self::$_isConnect)
            self::Connect();

        $query = trim($query);

        $time = microtime(true);
        self::$SQL = mysql_query($query, self::$CONNECT) or die(mysql_error() . "<br><b>" . $query . '</b>');
        $time1 = microtime(true);
        
        self::$TIME_QUERY += round(($time1 - $time), 4);
        self::$NUM_QUERY++;
        return self::$SQL;
    }

    static public function Data($_SQL=NULL) {
        if ($_SQL === NULL) {
            $tmp = mysql_fetch_assoc(self::$SQL);
            if (!is_null($tmp)) {
                return $tmp;
            } else {
                mysql_free_result(self::$SQL);
                return NULL;
            }
        } elseif ($_SQL !== NULL AND !is_resource($_SQL)) {
            $data = mysql_fetch_assoc(self::$SQL);
            return $data[$_SQL];
        } else {
            $tmp = mysql_fetch_assoc($_SQL);
            if (!is_null($tmp)) {
                return $tmp;
            } else {
                mysql_free_result($_SQL);
                return NULL;
            }
        }
    }

    static public function fullData($_SQL=NULL) {
        if (is_null($_SQL)) {
            $_SQL = self::$SQL;
        }
        $_TMP = array();

        while ($_DATA = self::Data($_SQL)) {
            $_TMP[] = $_DATA;
        }
        return $_TMP;
    }

    static public function Num($_SQL=NULL) {
        if ($_SQL === NULL) {
            return mysql_num_rows(self::$SQL);
        } else {
            return mysql_num_rows($_SQL);
        }
    }

    static public function Affected() {
        return mysql_affected_rows(self::$CONNECT);
    }

    static public function ID() {
        return mysql_insert_id(self::$CONNECT);
    }

    static public function Select($select, $data=NULL) {
        if ($data === NULL) {
            return self::Query($select);
        } else {
            if (!is_array($data)) {
                $values = array($data);
            } else {
                $values = $data;
            }

            $data = array();
            foreach ($values as $value) {
                $data[] = self::Secure($value);
            }

            return self::Query(self::Printf($select, $data));
        }
    }

    static public function Update($table, $data, $other=null) {
        $_data = null;
        $_value = null;
        foreach ($data as $index => $value) {
            if (substr($index, 0, 1) == '#') {
                $index = str_replace("#", "", $index);
                $_data .= "`" . $index . "`=" . $index . $value . ',';
            } else {
                if (is_null($value)) {
                    $_value = "NULL,";
                } else {
                    $_value = "'" . self::Secure($value) . "',";
                }
                $_data .= "`" . $index . "`=" . $_value;
            }
        }
        $_data = substr($_data, 0, -1);

        if (!is_null($other)) {
            if (is_array($other)) {
                $_where = 'WHERE ';
                foreach ($other as $index => $value) {
                    $_where .= "`" . $index . "`";

                    if (is_null($value)) {
                        $_where .= " IS NULL";
                    } elseif (is_array($value)) {
                        $_where .= " IN (";
                        foreach ($value as $v)
                            $_where .= "'" . self::Secure($v) . "',";

                        $_where = substr($_where, 0, -1);
                        $_where .= ")";
                    } else {
                        $_where .= "='" . self::Secure($value) . "'";
                    }

                    $_where .= " AND ";
                }

                $_where = substr($_where, 0, -5);
            } else {
                $_where = 'WHERE ' . $other;
            }
        } else {
            $_where = "";
        }

        self::Query("UPDATE " . $table . " SET " . $_data . " " . $_where);
    }

    static public function Insert($table, $data, $delay=FALSE, $duplicate = FALSE) {
        $_values = null;
        $_data = null;
        foreach ($data as $index => $value) {
            if (substr($index, 0, 1) == '#') {
                $index = str_replace("#", "", $index);
                $_data .= "`" . $index . "`,";
                $_values .= $value . ",";
            } else {
                $_data .= "`" . self::Secure($index) . "`,";
                if (is_null($value)) {
                    $_values .= "NULL,";
                } else {
                    $_values .= "'" . self::Secure($value) . "',";
                }
            }
        }
        $_data = substr($_data, 0, -1);
        $_values = substr($_values, 0, -1);

        $_duplicate = NULL;
        if ($duplicate !== FALSE) {
            $_duplicate = " ON DUPLICATE KEY UPDATE ";
            foreach ($duplicate as $i => $v) {
                if (substr($i, 0, 1) == '#') {
                    $i = str_replace("#", "", $i);

                    $_duplicate .= "`" . $i . "`";
                    $_duplicate .= "=";
                    $_duplicate .= "VALUES(" . $i . ")";
                    $_duplicate .= ",";
                } else {
                    $_duplicate .= "`" . $i . "`";
                    $_duplicate .= "=";
                    $_duplicate .= "'" . $v . "'";
                    $_duplicate .= ",";
                }
            }
            $_duplicate = substr($_duplicate, 0, -1);
        }

        return self::Query("INSERT" . ($delay === true ? " DELAYED" : "") . " INTO " . $table . " (" . $_data . ") VALUES (" . $_values . ")" . $_duplicate);
    }

    static public function Replace($table, $data) {
        $_values = null;
        $_data = null;
        foreach ($data as $index => $value) {
            if (substr($index, 0, 1) == '#') {
                $index = str_replace("#", "", $index);
                $_data .= "`" . $index . "`,";
                $_values .= $value . ",";
            } else {
                $_data .= "`" . self::Secure($index) . "`,";
                if (is_null($value)) {
                    $_values .= "NULL,";
                } else {
                    $_values .= "'" . self::Secure($value) . "',";
                }
            }
        }
        $_data = substr($_data, 0, -1);
        $_values = substr($_values, 0, -1);
        self::Query("REPLACE INTO " . $table . " (" . $_data . ") VALUES (" . $_values . ")");
    }

    static public function Delete($table, $data = array()) {
        $_data = null;

        foreach ($data as $index => $value) {
            if (!is_null($_data)) {
                $_data .= " AND ";
            } else {
                $_data .= " WHERE ";
            }

            $_data .= "`" . self::Secure($index) . "`";
            if (is_null($value)) {
                $_data .= "=NULL";
            } elseif (is_array($value)) {
                $_data .= " IN (";
                foreach ($value as $v)
                    $_data .= "'" . self::Secure($v) . "',";

                $_data = substr($_data, 0, -1);
                $_data.= ")";
            } else {
                $_data .= "='" . self::Secure($value) . "'";
            }
        }

        self::Query("DELETE FROM " . $table . $_data);
    }

    static public function TableRows($table) {
        if (isset(self::$TMP[$table]))
            return self::$TMP[$table];

        $sql = self::Query("SHOW COLUMNS FROM `" . $table . '`');
        if (self::Num() > 0) {
            $data = self::fullData($sql);
            return self::$TMP[$table] = $data;
        }
        return self::$TMP[$table] = array();
    }

    static public function Secure($value) {
        if (!self::$_isConnect) {
            self::Connect();
        }

        if (is_array($value)) {
            print_r(debug_backtrace());
        }
        return mysql_real_escape_string($value, self::$CONNECT);
    }

    static public function Printf($format, $arr) {
        return call_user_func_array('sprintf', array_merge((array) $format, $arr));
    }

}
