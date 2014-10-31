<?php

class SPF_Component_Session implements SPF_IComponent {

    public function __construct(&$core) {
    }
    
    public function __set($name, $value) {
        $_SESSION[$name] = $value;
    }

    public function __isset($name) {
        return isset($_SESSION[$name]);
    }

    public function __get($name) {
        if (!isset($_SESSION[$name])) {
            return null;
        }
        return $_SESSION[$name];
    }

    public function __unset($name) {
        unset($_SESSION[$name]);
    }

    public function clean() {
        foreach ($_SESSION as $index => $value) {
            if ($index == 'error')
                continue; unset($_SESSION[$index]);
        }
    }

    public function __toString() {
        return session_id();
    }

}