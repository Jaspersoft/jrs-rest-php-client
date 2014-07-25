<?php

namespace Jaspersoft\Service\Result;


class ReportExecutionStatus {

    /**
     * @var string
     */
    public $status;

    /**
     * @var string
     */
    public $requestId;

    /**
     * @var string
     */
    public $reportUri;

    /**
     * @var array
     */
    public $exports;

    public static function createFromJSON($json_obj) {

    }

    protected function jsonSerialize() {
        $result = array();

        return $result;
    }

    public function toJSON() {
        return json_encode($this->jsonSerialize());
    }
} 