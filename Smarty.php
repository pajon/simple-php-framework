<?php

class SPF_Smarty {

    static $_instance;
    static $_cache = null;
    private $data = array();
    private $smarty;

    public static function getInstance() {
        if (!self::$_instance) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    private function __construct() {
        require SPF_CLASS . 'Smarty/Smarty.class.php';
        $this->smarty = new Smarty;

        if (DEVELOPER === FALSE) {
            //$this->smarty->registerFilter('output', 'stripHTML'); 
            //$this->smarty->compile_check = FALSE;
        }
        //$this->smarty->force_compile = TRUE;

        //$this->smarty->caching = 0;

        $this->smarty->php_handling = Smarty::PHP_REMOVE;
        
        $this->smarty->default_modifiers = array('escape:"html"');
        $this->smarty->compile_dir = SPF_APP . '/Tmp/SmartyCompile/';
        $this->smarty->cache_dir = SPF_APP . '/Tmp/SmartyCache/';
        $this->smarty->template_dir = SPF_TEMPLATE;
        $this->smarty->plugins_dir[] = SPF_APP . '/Smarty/';
    }

    public function __set($name, $data) {
        $this->data[$name] = $data;
    }

    public function __isset($name) {
        return isset($this->data[$name]);
    }

    public function __get($name) {
        return (isset($this->data[$name]) ? $this->data[$name] : NULL);
    }

    public function getClass() {
        return $this->smarty;
    }

    public function clear($file) {
        $this->smarty->clearCache($file);
    }
    
    public function display($file) {
        foreach ($this->data as $i => $v) {
            $this->smarty->assign($i, $v);
        }
        $this->smarty->display($file, self::$_cache);
    }
    
    public function fetch($file) {
        foreach ($this->data as $i => $v) {
            $this->smarty->assign($i, $v);
        }
        return $this->smarty->fetch($file);
    }
    public function setTitle($value) {
        $this->title = $value;
    }

}
