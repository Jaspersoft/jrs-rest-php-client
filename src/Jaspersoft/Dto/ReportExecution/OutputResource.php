<?php

namespace Jaspersoft\Dto\ReportExecution;

use Jaspersoft\Dto\DTOObject;

class OutputResource extends DTOObject
{

    /**
     * MIME type of output resource
     *
     * @var string
     */
    public $contentType;

    /**
     * Name of the output resource as it appeares in a URI
     *
     * @var string
     */
    public $fileName;

    /**
     * Is this the final version of the resource?
     *
     * @var boolean
     */
    public $outputFinal;


} 