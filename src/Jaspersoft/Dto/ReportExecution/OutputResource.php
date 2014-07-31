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

    public static function createFromHeaders($headerSet)
    {
        $result = new self();
        $result->outputFinal = (isset($headerSet['output-final']) && $headerSet['output-final'] == "true") ? 'true' : 'false';
        if (isset($headerSet['Content-Disposition'])) {
            preg_match('/filename="(.*)"/', $headerSet['Content-Disposition'], $matches);
            $result->fileName = end($matches);
        }
        $result->contentType = (isset($headerSet['Content-Type'])) ? $headerSet['Content-Type'] : null;

        return $result;
    }


} 