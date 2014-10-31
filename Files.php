<?php

class ExceptionFileExists extends Exception {
    
}

class ExceptionFileUpload extends Exception {
    
}

class ExceptionFileType extends Exception {
    
}

class ExceptionFileValid extends Exception {
    
}

class ExceptionFileSize extends Exception {
    
}

class ExceptionFileDimension extends Exception {
    
}

class ExceptionDirWriteable extends Exception {
    
}

class ExceptionFileMove extends Exception {
    
}

class SPF_Files {

    private $types = array();
    private $size = NULL;
    private $dimension = NULL;

    static function valid($file) {
        $data = file_get_contents($file);
        $ext = str_replace(".", "", strtolower(strrchr($file, ".")));

        if (preg_match('#&(quot|lt|gt|nbsp);#i', $data)) {
            $image_safe = false;
        } elseif (preg_match("#&\#x([0-9a-f]+);#i", $data)) {
            $image_safe = false;
        } elseif (preg_match('#&\#([0-9]+);#i', $data)) {
            $image_safe = false;
        } elseif (preg_match("#([a-z]*)=([\`\'\"]*)script:#iU", $data)) {
            $image_safe = false;
        } elseif (preg_match("#([a-z]*)=([\`\'\"]*)javascript:#iU", $data)) {
            $image_safe = false;
        } elseif (preg_match("#([a-z]*)=([\'\"]*)vbscript:#iU", $data)) {
            $image_safe = false;
        } elseif (preg_match("#(<[^>]+)style=([\`\'\"]*).*expression\([^>]*>#iU", $data)) {
            $image_safe = false;
        } elseif (preg_match("#(<[^>]+)style=([\`\'\"]*).*behaviour\([^>]*>#iU", $data)) {
            $image_safe = false;
        } elseif (preg_match("#</*(applet|link|style|script|iframe|frame|frameset|html|src)[^>]*>#i", $data)) {
            $image_safe = false;
        } elseif ($ext == 'gif' && !imagecreatefromgif($file)) {
            $image_safe = false;
        } elseif ($ext == 'jpg' && !imagecreatefromjpeg($file)) {
            $image_safe = false;
        } elseif ($ext == 'png' && !imagecreatefrompng($file)) {
            $image_safe = false;
        } else {
            $image_safe = true;
        }
        return $image_safe;
    }

    static function size($file) {
        $i = GetImageSize($file);
        return array($i[0], $i[1]);
    }

    public function setType($type) {
        if (is_array($type)) {
            $this->types = $type;
        } else {
            $this->types[] = $type;
        }
    }

    public function setSize($size) {
        $this->size = $size;
    }

    public function setDimension($x, $y = NULL) {
        if (is_null($y))
            $y = $x;

        $this->dimension = array($x, $y);
    }

    public function upload($name, $file, $is_image = TRUE) {
        if (empty($_FILES[$name]['name']))
            throw new ExceptionFileExists;

        $ext = str_replace(".", "", strtolower(strrchr($_FILES[$name]['name'], ".")));

        if (!in_array($ext, $this->types))
            throw new ExceptionFileType;

        if (!is_uploaded_file($_FILES[$name]['tmp_name']))
            throw new ExceptionFileUpload;

        if (!is_null($this->size) && $_FILES[$name]['size'] > $this->size)
            throw new ExceptionFileSize;

        if ($is_image === TRUE && !SPF_Image::valid($_FILES[$name]['tmp_name']))
            throw new ExceptionFileValid;

        if (!is_null($this->dimension) && SPF_Image::size($_FILES[$name]['tmp_name']) != $this->dimension)
            throw new ExceptionFileDimension;

        if (!is_writable(str_replace(strrchr($file, "/"), "", $file)))
            throw new ExceptionDirWriteable;

        $file = str_replace('#EXT#', $ext, $file);

        if (!move_uploaded_file($_FILES[$name]['tmp_name'], $file))
            throw new ExceptionFileMove;
    }

}