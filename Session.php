<?php

class SPF_Session {

    static $_instance;

    private function __construct() {
        
    }

    public static function getInstance() {
        if (!self::$_instance) {
            self::$_instance = new self();
        } return self::$_instance;
    }

    function __set($name, $value) {
        $_SESSION[$name] = $value;
    }

    function __isset($name) {
        return isset($_SESSION[$name]);
    }

    function __get($name) {
        if (!isset($_SESSION[$name])) {
            return null;
        }
        return $_SESSION[$name];
    }

    function __unset($name) {
        unset($_SESSION[$name]);
    }

    function clean() {
        foreach ($_SESSION as $index => $value) {
            if ($index == 'error')
                continue; unset($_SESSION[$index]);
        }
    }

    function __toString() {
        return session_id();
    }

}