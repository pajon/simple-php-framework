<?php

class SPF_Form {

    private $Action = "";
    private $Method = "POST";
    private $Upload = FALSE;
    private $Submit;
    private $Items;

    
    public function __construct($Action) {
        $this->Action = $Action;
    }
    
    /*
     *  Set Method
     */
    public function setMethod($Type) {
        $this->Method = $Type;
        return $this;
    }
    
    /*
     *  Set Upload 
     */
    public function setUpload() {
        $this->Upload = TRUE;
        return $this;
    }

    /*
     *  Set SUbmit
     */
    public function setSubmit($String) {
        $this->Submit = $String;
        return $this;
    }

    /* 
     *  Add Input
     */
    public function AddInput($Type, $Name) {
        $item = new SPF_Form_Input($Type, $Name);
        $this->insert($item);
        return $item;
    }
    
    /*
     *  Add Select
     */
    public function AddSelect($Name) {
        $item = new SPF_Form_Select($Name);
        $this->insert($item);
        return $item;
    }
    
    /*
     *  Add Textarea
     */
    public function AddTextarea($Name) {
        $item = new SPF_Form_Textarea($Name);
        $this->insert($item);
        return $item;
    }
    
    /*
     *  Get action
     */
    public function getAction() {
           return $this->Action;
    }
    
    /*
     *  Get Method
     */
    public function getMethod() {
        return $this->Method;
    }
    
    /*
     *  Get Upload
     */
    public function getUpload() {
        return $this->Upload;
    }
    
    /*
     *  Get Submit
     */
    public function getSubmit() {
        return $this->Submit;
    }

    /* 
     *  Get Items
     */
    public function getItems() {
        return $this->Items;
    }
    
    
    /*
     *  Insert data
     */
    private function insert($item) {
        $this->Items[] = $item;
    }    
    
}