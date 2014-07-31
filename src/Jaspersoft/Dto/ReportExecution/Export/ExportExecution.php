<?php

namespace Jaspersoft\Dto\ReportExecution\Export;


use Jaspersoft\Dto\DTOObject;

class ExportExecution extends DTOObject {

    /**
     * Unique ID of Export Execution
     *
     * @var string
     */
    public $id;

    /**
     *
     * @var \Jaspersoft\Dto\ReportExecution\Options
     */
    public $options;

    /**
     * Describes state of export
     * (e.g: "ready", "failed", etc.)
     *
     * @var string
     */
    public $status;


} 