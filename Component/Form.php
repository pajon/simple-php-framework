<?php

class SPF_Component_Form implements SPF_IComponent {

    private $class = NULL;

    public function __construct(&$core) {
        ;
    }

    public function setClass($class) {
        $this->class = $class;
    }

    protected function generateForm() {
        $html = '';
        $html .= '<table';
        
        if(is_null($this->class) === FALSE)
                $html .= ' class="'.$this->class.'"';
        
        $html .= '>';
        
        echo '
            
        <tr>
            <th><label for=""></label></th>
            <td><input id="" type="text" size="" maxlength="" name="" /></td>
        </tr>
        
';




        $html .= '</table>';
        return $html;
    }

    public function __toString() {
        return $this->generateForm();
    }

}