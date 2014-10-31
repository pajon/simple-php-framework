<?php

class SPF_Token {

    public static $error = null;

    function __construct() {
        
    }

    private static function generateToken() {
        return md5(microtime(true) . mt_rand());
    }

    public static function Create($user, $type, $expire) {
        $token = self::generateToken();

        SPF_DB::Select("SELECT token_value, token_expire FROM spf_token WHERE token_user='%d' AND token_type='%s'", array($user, $type));
        if (SPF_DB::Num()) {
            $data = SPF_DB::Data();

            if ($data['token_expire'] < time()) {
                SPF_DB::Update('spf_token', array(
                    'token_value' => $token,
                    'token_time' => time(),
                    'token_expire' => time() + $expire
                ), array('token_user' => $user,'token_type' => $type));
                return $token;
            } else {
                SPF_DB::Update('spf_token', array(
                    'token_time' => time(),
                    'token_expire' => time() + $expire
                ), array('token_user' => $user,'token_type' => $type));
                return $data['token_value'];
            }
        } else {
            SPF_DB::Insert('spf_token', array(
                'token_user' => $user,
                'token_type' => $type,
                'token_value' => $token,
                'token_time' => time(),
                'token_expire' => time() + $expire
            ));
        }
        return $token;
    }

    public static function Check($user, $type, $token) {
        SPF_DB::Select("SELECT token_type,token_value, token_expire FROM spf_token WHERE token_user='%d' AND token_type='%s'", array($user, $type));
        if (SPF_DB::Num()) {
            $data = SPF_DB::Data();
            if ($data['token_value'] != $token) {
                return false;
            } elseif ($data['token_expire'] < time()) {
                SPF_DB::Delete('spf_token', array('token_user' => $user, 'token_type' => $type));
                return false;
            } else {
                SPF_DB::Delete('spf_token', array('token_user' => $user, 'token_type' => $type));
                return true;
            }
        } else {
            return false;
        }
    }

}