<?php

class SPF_Tmp {

    static function set($key, $value, $group = NULL) {
        if ($group !== NULL) {
            if (!file_exists(SPF_TMP . $group . '/'))
                mkdir(SPF_TMP . $group . '/');
        } else {
            $group = '';
        }

        file_put_contents(SPF_TMP . $group . '/' . $key . '.tmp', serialize($value));
    }

    static function get($key, $group = NULL) {
        $file = SPF_TMP . ($group === NULL ? '' : $group . '/') . $key . '.tmp';
        if (file_exists($file))
            return unserialize(file_get_contents($file));
        return FALSE;
    }

}