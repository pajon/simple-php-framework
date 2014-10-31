<?php

class SPF_Model {

    static $__scache = array();
    private $__cache = array();
    private $__c = NULL;
    private $__tmp_row = array();
    private $__count_rows = NULL;
    private $__foreign = array();
    #
    private $primary = NULL;

    /**
     *
     * @var type Primárny kľúč
     */
    protected $_primary = 'id';

    /**
     *
     * @var type Názov tabuľky
     */
    protected $_table = NULL;

    /**
     *
     * @var type Dáta z riatku identifikované primárnym kľúčom
     */
    protected $_data = NULL;
    protected $_prefix = NULL;
    protected $_useModel = TRUE;

    #

    #
    #

    final public function __construct() {
        if (is_null($this->_table))
            $this->_table = strtolower(get_class($this));

        $this->__c = new SPF_Model_Control($this->_table, ($this->_prefix === NULL ? NULL : $this->_prefix));

        if (!is_array($this->_primary))
            $this->_primary = array($this->_primary);

        $n = func_num_args();

        if ($n == 1) {
            $tmp = func_get_arg(0);
            if (is_array($tmp))
                $this->primary = $tmp;
            else
                $this->primary = array($tmp);
        }
        else
            for ($i = 0; $i < $n; $i++)
                $this->primary[] = func_get_arg($i);
    }

    public final function getID($row = FALSE) {
        if ($row == TRUE)
            return $this->__call('getId', NULL);

        if ($this->primary !== NULL) {
            if (count($this->primary) == 1)
                return $this->primary[0];
            else
                return $this->primary;
        }
    }

    public final function __call($name, $args) {

        if (strpos($name, 'get') === 0) {

            ## GET
            $arg = str_replace('get', '', $name);
            if (DEVELOPER === FALSE || $this->__c->hasTableRow($arg)) {

                ## CACHE ROWNAME
                if (!isset(self::$__scache[$this->_table . '-' . $arg]))
                    $row = self::$__scache[$this->_table . '-' . $arg] = $this->__c->RowName($arg);
                else
                    $row = self::$__scache[$this->_table . '-' . $arg];


                ## CACHE FOREIGN
                if ($this->_useModel == TRUE)
                    if (!isset($this->__cache['foreign'][$arg]))
                        $foreign = $this->__cache['foreign'][$arg] = $this->__c->getForeign($row);
                    else
                        $foreign = $this->__cache['foreign'][$arg];
                else
                    $foreign = FALSE;

                ## CHECK LOADED ROW
                if (isset($this->__tmp_row[$arg])) {
                    if ($foreign !== FALSE) {
                        if (array_key_exists($row, $this->__foreign))
                            return $this->__foreign[$row];

                        return $this->__foreign[$row] = $this->__c->getClass($foreign, $this->__tmp_row[$arg]);
                    }

                    return $this->__tmp_row[$arg];
                }

                # AK DATA VYBERAME PRVY KRAT, TAK ICH MUSIME NAJSKOR NACITAT
                if ($this->_data === NULL || !isset($this->_data[$row]))
                    $this->__loadData();

                if ($foreign !== FALSE) {
                    if (array_key_exists($row, $this->__foreign))
                        return $this->__foreign[$row];

                    return $this->__foreign[$row] = $this->__c->getClass($foreign, $this->_data[$row]);
                }

                return $this->_data[$row];
            }
            throw new Exception("Stĺpec s názvom \"" . $arg . "\" v tabuľke s názvom \"" . $this->_table . "\" neexistuje.");
        } elseif (strpos($name, 'set') === 0) {

            ## SET
            $arg = str_replace('set', '', $name);
            
            if ($arg == 'ID')
                $arg = 'id';
            
            if ($this->__c->hasTableRow($arg)) {
                if (count($args) < 1)
                    throw new Exception("Nezadná premenná");
                $this->__tmp_row[$arg] = $args[0];
                return $this;
            }
            throw new Exception("Stĺpec s názvom \"" . $arg . "\" v tabuľke s názvom \"" . $this->_table . "\" neexistuje.");
        } else {
            throw new Exception("Neznáma operácia \"" . get_class($this) . "->" . $name . "\"");
        }
    }

    public final function find($where = NULL, $order = NULL, $limit = NULL, $count = FALSE) {
        $table = $this->_table;
        $sql = "";
        $primary = "";

        if (isset($where))
            $sql .= $this->__c->buildWhere($where, $table);
        if (isset($order))
            $sql .= $this->__c->buildOrder($order, $table);
        if (isset($limit))
            $sql .= " LIMIT " . $limit;


        $n = count($this->_primary);
        for ($i = 0; $i < $n; $i++) {
            if (strpos($this->_primary[$i], '=') !== FALSE)
                list($this->_primary[$i], ) = explode("=", $this->_primary[$i]);

            $primary[] = $this->__c->RowName($this->_primary[$i]);
        }

        SPF_DB::Select("SELECT " . ($count ? 'COUNT(*) as num' : implode(',', $primary)) . " FROM " . $table . $sql);
        if ($count)
            return SPF_DB::Data('num');

        $object = array();
        $class = get_class($this);

        while ($tmp = SPF_DB::Data())
            $object[] = new $class(array_values($tmp));

        return $object;
    }

    public final function count() {
        if ($this->__count_rows == NULL) {
            SPF_DB::Select("SELECT COUNT(*) as num FROM " . $this->_table);
            $this->__count_rows = SPF_DB::Data('num');
        }
        return $this->__count_rows;
    }

    public final function build($data) {
        $tmp = array();
        if (!is_array($data))
            return new self($data);

        foreach ($data as $v)
            $tmp = new self($v);

        return $tmp;
    }

    public final function save($cond = NULL) {
        $rows = array();
        $where = array();

        if (count($this->__tmp_row) == 0)
            return FALSE;

        if ($cond === NULL) {
            $_primary = $this->_primary;
            $primary = $this->primary;

            $n = count($this->_primary);

            for ($i = 0; $i < $n; $i++) {
                if (strpos($_primary[$i], '=') !== FALSE) {
                    list($_primary[$i], $primary[$i]) = explode("=", $_primary[$i]);
                }
                $where[$this->__c->getPrefix() . '_' . $_primary[$i]] = $primary[$i];
            }
        }
        else
            foreach ($cond as $i => $v)
                $where[$this->__c->RowName($i)] = $v;

        # NASTAVINE DATA PRE ZMENU
        foreach ($this->__tmp_row as $i => $v)
            $rows[$this->__c->RowName($i)] = $v;

        # VYPRAZDNIME PRE PRIPADNE NESKORSIE POUZITIE
        $this->__tmp_row = array();

        SPF_DB::Update($this->_table, $rows, $where);

        # APLIKUJEME ZMENY AJ NA DATA NACITANE MODELOM
        foreach ($rows as $i => $v)
            $this->_data[$i] = $v;
    }

    public final function insert($afterClean = FALSE) {
        $this->__count_rows++;

        $rows = array();

        foreach ($this->__tmp_row as $i => $v) {
            $rows[$this->__c->RowName($i)] = $v;
        }
        $sql = SPF_DB::Insert($this->_table, $rows);

        if ($afterClean)
            $this->__tmp_row = array();

        return SPF_DB::ID($sql);
    }

    public final function delete($cond = NULL) {
        $where = array();

        if ($cond === NULL) {
            $_primary = $this->_primary;
            $primary = $this->primary;

            $n = count($this->_primary);
            for ($i = 0; $i < $n; $i++) {
                if (strpos($_primary[$i], '=') !== FALSE) {
                    list($_primary[$i], $primary[$i]) = explode("=", $_primary[$i]);
                }
                $where[$this->__c->getPrefix() . '_' . $_primary[$i]] = $primary[$i];
            }
        }
        else
            foreach ($cond as $i => $v)
                $where[$this->__c->RowName($i)] = $v;

        SPF_DB::Delete($this->_table, $where);
    }

    public final function truncate() {
        SPF_DB::Query("TRUNCATE TABLE `" . $this->_table . "`");
    }

    public final function exists() {
        if ($this->_data === NULL)
            $this->__loadData();

        return ($this->_data === FALSE ? FALSE : TRUE);
    }

    public final function __toString() {
        if (count($this->primary) == 1)
            return (string) $this->primary[0];
        else
            return implode(",", $this->primary);
    }

    private final function __loadData() {
        $sql = "SELECT * FROM " . $this->_table;
        $sql .= " WHERE ";

        $n = count($this->_primary);
        $_primary = $this->_primary;
        $primary = $this->primary;

        for ($i = 0; $i < $n; $i++) {
            if (strpos($_primary[$i], '=') !== FALSE)
                list($_primary[$i], $primary[$i]) = explode("=", $_primary[$i]);

            $sql .= $this->__c->RowName($_primary[$i]);
            $sql .= "='" . $primary[$i] . "'";
            if ($i != $n - 1)
                $sql .= " AND ";
        }

        $this->_data = SPF_DB::Data(SPF_DB::Query($sql));
    }

    /* NEW */
}