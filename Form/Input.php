<?php

class SPF_Form_Input {

    private $Name;
    private $Type = "text";
    private $Value;
    private $Placeholder = NULL;
    private $Style = NULL;
    private $jQuery = FALSE;
    private $Required = FALSE;
    private $Caption = NULL;


    public function __construct( $Type, $Name) {
        $this->Name = $Name;
        $this->Type = $Type;
    }

    /*
     *  Set Caption
     */
    public function setCaption($Text) {
        $this->Caption = $Text;
        return $this;
    }

    /*
     *  Set value
     */
    public function setValue($String) {
        $this->Value = $String;
        return $this;
    }

    /*
     *  Set Placeholder
     */
    public function setPlaceholder($String) {
        $this->Placeholder = $String;
        return $this;
    }

    /*
     *  Set Style
     */
    public function setStyle($String) {
        $this->Style = $String;
        return $this;
    }

    /*
     *  Set jQuery
     */
    public function jQuery() {
        $this->jQuery = TRUE;
        return $this;
    }

    /*
     *  Set is Required
     */
    public function isRequired() {
        $this->Required = TRUE;
        return $this;
    }

    /*
     *  Get Name
     */
    public function getName() {
        return $this->Name;
    }
    
    /*
     *  Get Type
     */
    public function getType() {
        return $this->Type;
    }
    
    /*
     *  Get Value
     */
    public function getValue() {
        return $this->Value;
    }
    
    /* 
     *  Get Placeholder
     */
    public function getPlaceholder() {
        return $this->Placeholder;
    }
    
    /*
     *  Get Style
     */
    public function getStyle() {
        return $this->Style;
    }
    
    /*
     *  Get Style
     */
    public function getjQuery() {
        return $this->jQuery;
    }
    
    /*
     *  Get Required
     */
    public function getRequired() {
        return $this->Required;
    }
    
    /*
     *  Get Caption
     */
    public function getCaption() {
        return $this->Caption;
    }
    
    /*
     *  Get Element
     */
    public function getElement() {
        return 1;
    }
    
 
}