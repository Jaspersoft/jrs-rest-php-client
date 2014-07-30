<?php

namespace Jaspersoft\Exception;


class ReportExecutionException extends \Exception {

    public $message;
    public $parentException;

    public function __construct($message = null, $parentException = null)
    {
        $this->message = $message;
        $this->parentException = $parentException;
    }



} 