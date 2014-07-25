<?php

namespace Jaspersoft\Dto\ReportExecution;

use Jaspersoft\Dto\DTOObject;

class Export extends DTOObject {

    /**
     * Unique ID of export
     *
     * @var string
     */
    private $id;

    /**
     * Collection of export option parameters
     *
     * @var array
     */
    private $options;

    /**
     * Status of export
     *
     * @var string
     */
    private $status;

    /**
     * Description of error which may have occured
     *
     * @var string
     */
    private $errorDescriptor;

    /**
     * Metadata about the type of output of the resource
     *
     * @var Object
     */
    private $outputResource;

    /**
     *
     *
     * @var array
     */
    private $attachments;

} 