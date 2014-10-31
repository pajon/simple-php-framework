<?php

class SPF_File {

    static function ListDir($root, $only_files = false, $list = array()) {
        $dir = (array)glob($root . '*');
        foreach ($dir as $file) {

            $list[] = $file;
            if (is_dir($file)) {
                self::listdir($file . "/*", $only_files, $list);
            }
        }
        if ($only_files) {
            $list = str_replace(array($root, '/'), '', $list);
        }
        return $list;
    }

    function UploadList() {
        return $_FILES;
    }

    static function isUploaded($name) {
        if (!isset($_FILES[$name]) || $_FILES[$name]['error'] == '4')
            return FALSE;
        else
            return TRUE;
    }

    static function Upload($name, $to, $ext = array('.png', '.jpeg', '.jpg', '.gif'), $size = 1000000, $_size = null) {

        if (empty($_FILES[$name]['name'])) {
            trigger_error("Súbor s názvom \"" . $name . "\" nenájdený!");
            return;
        }

        if (!in_array(strtolower(strrchr($_FILES[$name]['name'], ".")), $ext)) {
            return "Súbor nemá požadovaný typ.";
        }

        if (!is_uploaded_file($_FILES[$name]['tmp_name'])) {
            return "Súbor nenájdený. Skúste akciu zopakovať ešte raz.";
        }

        if ($_FILES[$name]['size'] > $size) {
            return "Súbor je väčší ako je povolené.";
        }

        if (!SPF_Image::valid($_FILES[$name]['tmp_name'])) {
            return "Súbor obsahuje nebezpečný kód. Z týchto dôvodov ho nie je možné nahrať. Prosím kontaktujte administrátora";
        }

        if (!is_null($_size) && SPF_Image::size($_FILES[$name]['tmp_name']) != $_size) {
            return "Súbor nemá stanovenú veľkosť " . $_size[0] . "x" . $_size[1] . "px.";
        }

        if (!is_writable(str_replace(strrchr($to, "/"), "", $to))) {
            return "Súbor nie je možné vytvoriť. Prosím kontaktujte administrátora";
        }

        if (!move_uploaded_file($_FILES[$name]['tmp_name'], $to)) {
            return "Súbor sa nepodarilo uložiť!";
        }
        return true;
    }

    static function Delete($dir) {
        if (!file_exists($dir))
            return true;
        if (!is_dir($dir) || is_link($dir))
            return unlink($dir);
        foreach (scandir($dir) as $item) {
            if ($item == '.' || $item == '..')
                continue;
            if (!self::Delete($dir . "/" . $item)) {
                if (!self::Delete($dir . "/" . $item))
                    return false;
            }
        }
        return rmdir($dir);
    }

}