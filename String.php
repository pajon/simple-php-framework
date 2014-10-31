<?php
class SPF_String {
    static function urlclear($url) {
        return rawurlencode(iconv("utf-8", "us-ascii//TRANSLIT", str_replace(array(" ", ":", ".",";","?","!"), array('-',"","","","",""), $url)));
    }
}