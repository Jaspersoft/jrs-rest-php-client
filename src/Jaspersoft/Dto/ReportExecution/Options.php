<?php

namespace Jaspersoft\Dto\ReportExecution;


use Jaspersoft\Dto\DTOObject;

class Options extends DTOObject {

    /**
     * The type of output (e.g: html, pdf, xls, etc...)
     *
     * @var string
     */
    public $outputFormat;

    /**
     * A string to be prefixed on the URLs of attachments in an HTML output
     *
     * @var string
     */
    public $attachmentsPrefix;

    /**
     * Are inline scripts allowed?
     *
     * @var boolean
     */
    public $allowInlineScripts;

} 