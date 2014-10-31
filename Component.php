<?php

class SPF_Component {

    private static $_instance;
    private $cache = array();

    public static function getInstance() {
        if (!self::$_instance) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    public function create($name, SPF_IComponent $class, $cache) {
        if ($cache) {
            $this->setCache($name, $class);
        }
        return $class;
    }

    public function buildClass($component) {
        return 'SPF_Component_' . ucfirst($component);
    }

    public function setCache($name, SPF_IComponent $class) {
        $this->cache[$name] = $class;
    }
    
    public function getCache($component = NULL) {
        if (is_null($component))
            return $this->cache;

        return $this->cache[$component];
    }

}