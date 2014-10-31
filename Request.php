<?php
class SPF_Request {
	
	static $_instance;
	
	private function __construct() { }
	
	public static function getInstance() {
            if(!self::$_instance) { self::$_instance = new self(); }
            return self::$_instance;
	}
	
	public function isPOST() {
		return ($_SERVER["REQUEST_METHOD"]=='POST'?true:false);
	}
	
	public function isGET() {
		return ($_SERVER["REQUEST_METHOD"]=='GET'?true:false);
	}
	
	public function isAJAX() {
		return (array_key_exists('X_REQUESTED_WITH',$_SERVER) && $_SERVER['X_REQUESTED_WITH']=='XMLHttpRequest');
	}
	
	public function getUserAgent() {
		return $_SERVER["HTTP_USER_AGENT"];
	}
	
	public function getUserLang() {
		return $_SERVER["HTTP_ACCEPT_LANGUAGE"];
	}
	
	public function getUserCharset() {
		return $_SERVER["HTTP_ACCEPT_CHARSET"];
	}

        public function getURL($full=false) {
            if($full==false) { list($url,) = explode("?",$_SERVER['REQUEST_URI'],2); return $url; }
            else { return $_SERVER['REQUEST_URI']; }
        }

        public function getServer() {
            return $_SERVER["SERVER_NAME"];
        }
}