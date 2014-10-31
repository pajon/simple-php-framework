<?php

class SPF_Auth_Exception_Login extends Exception {
    
}

class SPF_Auth {

    static $_instance;
    private $id;

    public static function getInstance($id = NULL) {
        if (!self::$_instance) {
            self::$_instance = new self($id);
        } return self::$_instance;
    }

    protected function __construct($id = NULL) {
        $this->isAuth = NULL;
        $this->id = NULL;
        $this->session = SPF_Session::getInstance();
        $this->auth();
    }

    public function getID() {
        return $this->id;
    }

    public function auth() {
        if (!is_null($this->isAuth))
            return $this->isAuth;

        $u = new SPF_User();
        $tmp = $u->find(array('session=' . $this->session()));
        if (count($tmp) == 1) {
            $this->id = $tmp[0]->getID();
            return $this->isAuth = TRUE;
        } else {
            return $this->isAuth = FALSE;
        }
    }

    public function login($login, $pass) {
        if ($this->auth() !== FALSE)
            return TRUE;

        $user = new SPF_User();
        $tmp = $user->find(array('email=' . $login));

        if (count($tmp) > 0) {
            $tmp = $tmp[0];

            if ($tmp->getPassword() != $this->hashPassword($pass))
                throw new SPF_Auth_Exception_Login($tmp->getID());

            $tmp->setSession($this->session())->save();
            return $tmp->getID();
        }
        return FALSE;
    }

    public function logout() {
        $user = new SPF_User($this->id);
        $user->setSession(NULL);
        $user->save();
    
    }
    
    public function setLogout($user) {
        if ($this->auth() === FALSE)
            return TRUE;

        $user->setSession(NULL)->save();
    }

    public function hashPassword($pass) {
        return sha1($pass);
        //$salt = substr(sha1($pass), 0, 5);
        //return sha1(hash("sha512", $salt . $pass . $salt));
    }

    private function session() {
        return md5($_SERVER['REMOTE_ADDR'] . (isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : "") . session_id());
    }

}