<?php

abstract class SPF_Module {

    private $core;
    protected $auth;
    public $request;
    public $smarty;
    public $template;
    protected $args;
    protected $user;

    final public function __construct(SPF_Application &$core) {
        $this->core = &$core;
        $this->args = &$core->args;
        $this->request = &$core->request;
        $this->smarty = &$core->smarty;
        $this->user = &$core->user;
        $this->auth = &$core->auth;
    }

    final public function __get($name) {
        throw new Exception("Unidentified class variable \"" . $name . "\"");
    }

    final protected function location($location) {
        SPF_Application::location($location);
    }

    final protected function checkToken($type, $url = NULL) {
        if (!isset($_POST['token']))
            trigger_error('Neexistujuci zaznam "token" vo formulári');

        if (!SPF_Token::Check($this->user->getID(), $type, $_POST['token'])) {
            error("Formulár bol odoslaný po limite! Akciu musíte opakovať ešte raz!", FALSE);
            $this->location($url == NULL ? '/' : $url);
        }
    }

    final protected function loadComponnent($component, $cache = TRUE) {
        $c = SPF_Component::getInstance();

        if ($cache)
            if (in_array($component, $c->getCache()))
                return $c->getCache($component);

        $name = $c->buildClass($component);
        return $c->create($component, new $name($this->core), $cache);
    }

    final protected function useRender($render = FALSE) {
        $this->core->rendering = $render;
    }

    public function loader() {
        return TRUE;
    }

    public function beforeExec($method) {
        return TRUE;
    }

    public function afterExec($method) {
        return TRUE;
    }

}