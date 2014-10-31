<?php

class SPF_Core_Path {

    private $module;
    private $action;
    private $arguments;

    function __construct() {
        return $this;
    }

    public function getModule() {
        if ($this->module === NULL)
                $this->parseURL();
            return $this->module;
    }
    
    public function getAction() {
        if ($this->action === NULL)
                $this->parseURL();
            return $this->action;
    }
    
    public function getArguments() {
        if ($this->arguments === NULL)
                $this->parseURL();
            return $this->arguments;
    }

    private function parseURL() {
        list($url, ) = explode("?", $_SERVER['REQUEST_URI'], 2);
        $url = explode('/', $url);
        foreach ($url as $i => $v)
            if (trim($v) == '')
                unset($url[$i]);

        $url = array_values($url);
        $count = count($url);

        if ($count >= 2) {
            $this->module = ucfirst($url[0]);
            $this->action = $url[1];

            if ($count > 2) {
                for ($i = 2; $i <= ($count - 1); $i++) {
                    $this->arguments[] = $url[$i];
                }
            } // SET ARGS
        } elseif ($count == 1) {
            $this->module = $url[0];
            $this->action = 'Index';
        } else {
            $this->module = SPF_DEFAULT_MODULE;
            $this->action = 'Index';
        }
    }
}