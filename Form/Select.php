<?php

class SPF_Form_Select {

    private $Name;
    private $Style = NULL;
    private $jQuery = FALSE;
    private $Required = FALSE;
    private $Selected = FALSE;
    private $Caption = NULL;
    private $Values = array();

    
    public function __construct($Name) {
        $this->Name = $Name;
    }

    /*
     *  Set Values
     */
    public function setValues($Array) {
        $this->Values = $Array;
        return $this;
    }

    /*
     *  Set Caption
     */
    public function setCaption($Text) {
        $this->Caption = $Text;
        return $this;
    }    
    
    
    /*
     * Set Selected
     */
    public function setSelected($Var) {
        $this->Selected = $Var;
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
     *  Get Selected
     */
   public function getSelected() {
       return $this->Selected;
   }
    
    /*
     * Get Values
     */
    public function getValues() {
       return $this->Values;
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
        return 2;
    }   
    
}