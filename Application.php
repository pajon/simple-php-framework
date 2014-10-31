<?php

class SPF_Application {

    static $_instance;
    public $_class;
    public $request;
    public $rendering = TRUE;
    public $smarty;
    public $args;
    public $user;
    public $auth;
    public $controllers = array();
    public $view;

    public static function getInstance() {
        if (!self::$_instance) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    private function __construct() {
        $this->request = SPF_Request::getInstance();
        $this->smarty = SPF_Smarty::getInstance();
        $this->view = new SPF_View();
        $this->run();
    }

    public function run() {
        error_reporting(-1);
        if (DEVELOPER === FALSE)
            ini_set('display_errors', FALSE);
        else
            ini_set('display_errors', TRUE);

        $this->auth = SPF_Auth::getInstance();
        $this->smarty->auth = $this->auth->auth();
        $this->smarty->user = $this->user = new SPF_User($this->auth->getID());
        $this->smarty->admin = $this->user->isAdmin();

        $this->smarty->device = $this->view->getDevice();

        // Last visit
        $user = new SPF_User($this->auth->getID());
        $user->setLastvisit(time());
        $user->save();

        $route = new SPF_Core_Path();
        $module = 'SPF_Controller_' . ucfirst($route->getModule()) . '';
        $method = ucfirst(str_replace("-", "_", $route->getAction()));
        $data = $route->getArguments();

        if ($this->request->isPOST()) {
            $method = 'action' . $method;
            $data = $_POST;
        }

        $this->args = $data;

        foreach (glob(SPF_CONTROLLER . '*', GLOB_NOSORT) as $v) {
            include($v);
            if ($this->request->isGET()) {
                $class = 'SPF_Controller_' . ucfirst(str_replace(array(SPF_CONTROLLER, '.php'), '', $v));
                $object = new $class($this);
                $object->loader();
                unset($object);
            }
        }


        if (!in_array($module, get_declared_classes()) /* || !in_array($method, get_class_methods($module)) */) {
            $module = 'SPF_Controller_' . SPF_DEFAULT_MODULE;
            $method = 'Index';
        } elseif (!in_array($method, get_class_methods($module))) {
            $method = 'Index';
        }

        $m = new $module($this);
        $m->beforeExec($method);
        $m->$method($this->args);
        $m->afterExec($method);

        if ($this->request->isPOST())
            location('/');

        $this->render($route->getModule(), $method);
    }

    final private function render($module, $page) {
        if ($this->rendering === TRUE) {
            $module = ucfirst($module);
            $page = ucfirst($page);

            $tpl = SPF_TEMPLATE . $module . '/' . $page . '.tpl';
            if (!file_exists($tpl))
                location('/');

            $this->smarty->tpldir = SPF_TEMPLATE . $module . '/';
            $this->smarty->display($tpl);
        }
    }

    final public static function location($location) {
        header('Location: ' . $location);
        exit;
    }

}