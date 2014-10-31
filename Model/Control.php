<?php

class SPF_Model_Control {

    static $cache = array();
    private $table_rows = NULL;
    private $table = NULL;
    private $foreigns = NULL;
    private $prefix = NULL;

    public function __construct($table, $prefix = NULL) {
        $this->table = $table;
        $this->prefix = $prefix;
    }

    public final function getPrefix() {
        if ($this->prefix !== NULL)
            return $this->prefix;

        $tmp = explode("_", $this->table);
        return $this->prefix = $tmp[(count($tmp) - 1)];
    }

    public final function TableRows() {
        if (DEVELOPER === FALSE) {
            $tmp = SPF_Tmp::get($this->table, 'TableRows');

            if ($tmp !== FALSE)
                $this->table_rows = $tmp;
        }

        if ($this->table_rows === NULL) {
            $this->table_rows = SPF_DB::TableRows($this->table);
            if (DEVELOPER === FALSE)
                SPF_Tmp::set($this->table, $this->table_rows, 'TableRows');
        }

        return $this->table_rows;
    }

    public final function hasTableRow($row, $table = NULL) {
        if ($table === NULL)
            $table = $this->table;

        $rows = $this->TableRows($table);
        $prefix = $this->getPrefix();

        $row = $this->RowName($row);
        foreach ($rows as $_row) {
            if ($_row['Field'] == $row)
                return TRUE;
        }
        return FALSE;
    }

    public final function RowName($name) {
        $a = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z');
        $b = array('_a', '_b', '_c', '_d', '_e', '_f', '_g', '_h', '_i', '_j', '_k', '_l', '_m', '_n', '_o', '_p', '_q', '_r', '_s', '_t', '_u', '_v', '_w', '_x', '_y', '_z');
        $row = str_replace($a, $b, $name);
        return ($this->prefix !== NULL ? $this->prefix : $this->getPrefix()) . (strpos($row, '_') === 0 ? '' : '_' ) . $row; // .'_'.strtolower(preg_replace('/([^\s])([A-Z])/', '\1_\2', $name));
    }

    public final function getClass($table, $primary) {
        $tmp = explode("_", $table);
        $class = strtoupper($tmp[0]);
        unset($tmp[0]);
        $tmp = array_values($tmp);
        foreach ($tmp as &$v)
            $v = ucfirst($v);
        $class = $class . '_' . implode("_", $tmp);
        return new $class($primary);
    }

    #
    ##
    ###
    #############################################
    ###
    ##

    #
    
    public final function buildOrder($_SQL_, $table = NULL) {
        if (is_null($table))
            $table = $this->_table;
        $sql = "";
        if (!is_array($_SQL_))
            $_SQL_ = array($_SQL_);

        $sql .= " ORDER BY ";
        foreach ($_SQL_ as $v) {
            $ord = NULL;
            $tmp = explode(" ", $v);
            if (count($tmp) == 1) {
                $row = $v;
                $type = "";
            } else {
                $row = $tmp[0];
                $type = $tmp[1];
            }

            if (strpos($row, '=') !== FALSE) {
                list($row, $ord) = explode("=", $row);
            }

            if (DEVELOPER === FALSE || $this->hasTableRow($row, $table)) {
                if ($ord === NULL) {
                    $sql .= $this->getPrefix() . '_' . $row;
                    $sql .= " " . $type . ",";
                } else {
                    $sql .= '(' . $this->getPrefix() . '_' . $row . '=\'' . $ord . '\')';
                    $sql .= " " . $type . ",";
                }
            } else {
                throw new Exception("Neexistuje stlpec \"" . $row . "\" v tabulke \"" . $table . "\"");
            }
        }
        $sql = substr($sql, 0, -1);

        return $sql;
    }

    public final function buildJoin($_SQL_, $table = NULL) {
        if (is_null($table))
            $table = $this->_table;
        $sql = "";
        if (!is_array($_SQL_))
            $_SQL_ = array($_SQL_);

        foreach ($_SQL_ as $v) {
            $tmp = $this->_joins[$v];
            $sql .= " INNER JOIN ";
            $sql .= $tmp[0];
            $sql .= " ON ";
            list($frow, $lrow) = explode("=", $tmp[1]);

            $sql .= $this->__control->getPrefix($tmp[0]) . '_' . $frow;
            $sql .= "=";
            $sql .= $this->__control->getPrefix() . '_' . $lrow;
        }

        return $sql;
    }

    public final function buildWhere($_SQL_, $table = NULL) {
        if (is_null($table))
            $table = $this->_table;
        $sql = "";
        if (!is_array($_SQL_))
            $_SQL_ = array($_SQL_);

        $sql .= " WHERE ";
        foreach ($_SQL_ as $v) {
            if ($v === NULL)
                continue;

            if (strpos($v, '=') !== FALSE) {
                list($row, $val) = explode("=", $v, 2);

                $sql .= $this->getPrefix() . '_' . $row;
                if ($val != 'NULL') {
                    $sql .= "=";
                    $sql .= "'" . SPF_DB::Secure($val) . "'";
                } else {
                    if (strpos($v, '!=') !== FALSE)
                        $sql .= " IS NOT NULL";
                    else
                        $sql .= " IS NULL";
                }
            } else 
               $sql .= $this->getPrefix() . '_' . $v; 
            $sql .= " AND ";
        }
        $sql = substr($sql, 0, -5);

        return $sql;
    }

    public final function getForeign($row) {
        if (isset(self::$cache[$this->table])) {
            $this->foreigns = self::$cache[$this->table];
        } elseif (DEVELOPER === FALSE) {
            $tmp = SPF_Tmp::get($this->table, 'Foreign');
            if ($tmp !== FALSE)
                $this->foreigns = self::$cache[$this->table] = $tmp;
        }

        if ($this->foreigns === NULL || array_key_exists($row, $this->foreigns) == FALSE) {
            $Ga = array("RESTRICT", "CASCADE", "SET NULL", "NO ACTION");
            $R = '`(?:[^`]|``)+`';
            SPF_DB::Query("SHOW CREATE TABLE " . $this->table, 1); //  . $this->_table
            $sql = SPF_DB::Data('Create Table');

            preg_match_all("~CONSTRAINT ($R) FOREIGN KEY \\(((?:$R,? ?)+)\\) REFERENCES ($R)(?:\\.($R))? \\(((?:$R,? ?)+)\\)(?: ON DELETE (" . implode("|", $Ga) . "))?(?: ON UPDATE (" . implode("|", $Ga) . "))?~", $sql, $fa, PREG_SET_ORDER);
            foreach ($fa as $m) {
                preg_match_all("~$R~", $m[2], $xa);
                if (count($xa[0]) == 1)
                    $this->foreigns[str_replace('`', '', $xa[0][0])] = str_replace('`', '', $m[4] != "" ? $m[4] : $m[3]);
            }

            if (array_key_exists($row, (array) $this->foreigns) == FALSE)
                $this->foreigns[$row] = FALSE;

            if (DEVELOPER === FALSE)
                SPF_Tmp::set($this->table, $this->foreigns, 'Foreign');
        }

        return $this->foreigns[$row];
    }

}