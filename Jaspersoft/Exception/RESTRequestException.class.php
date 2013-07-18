<?php
namespace Jaspersoft\Exception;

class RESTRequestException extends \Exception {

    public function __construct($message) {
        $this->message = $message;
    }

}

?>