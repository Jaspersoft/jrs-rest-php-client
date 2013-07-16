<?php
namespace jaspersoft;

class RESTRequestException extends \Exception {

    public function __construct($message) {
        $this->message = $message;
    }

}

?>