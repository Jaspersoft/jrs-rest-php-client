<?php

namespace Jaspersoft\Dto\ReportExecution;

use Jaspersoft\Dto\DTOObject;

class Export extends DTOObject {

    /**
     * Unique ID of export
     *
     * @var string
     */
    public $id;

    /**
     * Collection of export option parameters
     *
     * @var array
     */
    public $options;

    /**
     * Status of export
     *
     * @var string
     */
    public $status;

    /**
     * Description of error which may have occured
     *
     * @var string
     */
    public $errorDescriptor;

    /**
     * Metadata about the type of output of the resource
     *
     * @var Object
     */
    public $outputResource;

    /**
     *
     *
     * @var array
     */
    public $attachments;

    public static function createFromJSON($json_data) {
        $result = new self();
        foreach ($json_data as $k => $v) {
            $result->$k = $v;
        }
        return $result;
    }

} 