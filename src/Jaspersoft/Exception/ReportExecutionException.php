<?php

namespace Jaspersoft\Exception;


class ReportExecutionException extends \Exception {

    const REPORT_COMPLETE_OR_NOT_FOUND = "The report execution was not found, or has already been completed.";
    private $message;

    public function __construct($message = null) {

    }



} 