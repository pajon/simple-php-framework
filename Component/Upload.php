<?php

class SPF_Component_Upload implements SPF_IComponent {

    public function __construct(&$core) {
        ;
    }
    
    protected function getImageSize($file) {
        list($width, $height, , ) = getimagesize($file);
        return array($width, $height);
    }
    
    public function isIpload($name) {
        if (!isset($_FILES[$name]) || $_FILES[$name]['error'] == '4')
            return FALSE;
        else
            return TRUE;
    }
    
    public function Upload($name, $to, $ext = array('.png', '.jpeg', '.jpg', '.gif'), $size = 1000000, $_size = null) {

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

        if (!is_null($_size) && $this->getImageSize($_FILES[$name]['tmp_name']) != $_size) {
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


}