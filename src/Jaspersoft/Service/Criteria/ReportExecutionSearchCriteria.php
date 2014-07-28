<?php

namespace Jaspersoft\Service\Criteria;

use Jaspersoft\Service\Criteria\Criterion;

class ReportExecutionSearchCriteria extends Criterion
{
    /**
     * URI of Report to search for executions
     *
     * @var string
     */
    public $reportURI;

    /**
     * Report job ID (scheduler)
     *
     * @var int
     */
    public $jobID;

    /**
     * Report job label (scheduler)
     *
     * @var string
     */
    public $jobLabel;

    /**
     * User who created job (scheduler)
     *
     * @var string
     */
    public $userName;

    /**
     * Report job fire time from criteria (scheduler)
     *
     * @var string
     */
    public $fireTimeFrom;

    /**
     * Report job fire time to criteria (scheduler)
     *
     * @var string
     */
    public $fireTimeTo;


}