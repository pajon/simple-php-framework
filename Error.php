<?php

class SPF_Error {

    static $_instance;

    private function __construct() {
        
    }

    public static function getInstance() {
        if (!self::$_instance) {
            self::$_instance = new self();
        } return self::$_instance;
        }

    public function init($error, $url) {
        if (isset($error) && $url != '/Error/') {
            location('/Error/');
        }
    }
}
