<?php

namespace Jaspersoft\Exception;


class ReportExecutionException extends \Exception {

    const REPORT_COMPLETE_OR_NOT_FOUND = "The report execution was not found, or has already been completed.";
    const SEARCH_NO_RESULTS = "The search yielded no results";
    public $message;
    public $parentException;

    public function __construct($message = null, $parentException = null)
    {
        $this->message = $message;
        $this->parentException = $parentException;
    }



} 