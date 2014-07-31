<?php

namespace Jaspersoft\Dto\ReportExecution\Export;

use Jaspersoft\Dto\DTOObject;

class Request extends DTOObject {

    /**
     * Format to export to (html, pdf, xls, etc.)
     *
     * @var string
     */
    public $outputFormat;

    /**
     * Range or number of page(s) to export
     * (e.g: "1" or "5-8")
     *
     * @var string
     */
    public $pages;

    /**
     * A URL prefix for report attachments. (only affects HTML output)
     * This string can contain some variables which will be replaced by server at runtime:
     *   {contextPath} : the webapp directory (e.g: /jasperserver-pro)
     *   {reportExecutionId} : the ID related to this reportExecution
     *   {exportExecutionId} : the ID of the export
     *
     * @var string
     */
    public $attachmentsPrefix;

    /**
     * Should scripts be included inline in the report output?
     *
     * @var boolean
     */
    public $allowInlineScripts;

    /**
     * JRS Deployment URL
     * (e.g: http://localhost:8080/jasperserver-pro)
     *
     * @var string
     */
    public $baseUrl;




} 