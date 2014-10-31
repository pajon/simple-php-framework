<?php

class SPF_Component_Image implements SPF_IComponent {

    private $file = NULL;
    private $object = NULL;

    function __construct(&$core) {
        
    }

    function setFile($file) {
        $this->file = $file;
        $this->object = new Imagick($file);
    }

    function setQuality($quality) {
        $this->object->setImageCompressionQuality($quality);
    }

    function setResize($x, $y) {
        $this->object->resizeImage($x, $y, null, 1);
    }
    
    function save($file = NULL) {
        if (is_null($file)) {
            $thumb->writeImage($this->file);
        } else {
            $thumb->writeImage($file);
        }
        
        $thumb->destroy();
    }

}