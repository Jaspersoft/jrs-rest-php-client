<?php

namespace Jaspersoft\Dto\ReportExecution\Export;

use Jaspersoft\Dto\DTOObject;
use Jaspersoft\Dto\ReportExecution\Attachment;
use Jaspersoft\Dto\ReportExecution\Options;
use Jaspersoft\Dto\ReportExecution\OutputResource;

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
     * Description of error which may have occurred
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
     * Collection of attachments in this export (images, etc.)
     *
     * @var array
     */
    public $attachments;

    public static function createFromJSON($json_data) {
        $result = new self();
        foreach ($json_data as $k => $v) {
            if (!empty ($v)) {
                if (is_array($v)) {
                    if ($k == Attachment::jsonField(true)) {
                        $attachments = array();
                        foreach ($v as $attachment) {
                            $attachments[] = Attachment::createFromJSON($attachment);
                        }
                        $result->$k = $attachments;
                    }
                } elseif (is_object($v)) {
                    if ($k == OutputResource::jsonField())
                        $result->$k = OutputResource::createFromJSON($v);
                    if ($k == Options::jsonField())
                        $result->$k = Options::createFromJSON($v);
                } else {
                    $result->$k = $v;
                }
            }
        }
        return $result;
    }

} 