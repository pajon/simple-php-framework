<?php

include(SPF_CLASS . 'Mailer/phpmailer.inc.php');

class SPF_Mailer extends phpmailer {

    function __construct() {
        $this->FromName = "Mailer";
    }

}