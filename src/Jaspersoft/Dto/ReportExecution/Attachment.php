<?php

namespace Jaspersoft\Dto\ReportExecution;


use Jaspersoft\Dto\DTOObject;

class Attachment extends DTOObject {

    /**
     * MIME type that should be given with Accept header to request attachment
     *
     * @var string
     */
    public $contentType;

    /**
     * Name of attachment
     *
     * @var string
     */
    public $fileName;



} 