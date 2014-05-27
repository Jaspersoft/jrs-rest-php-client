<?php
namespace Jaspersoft\Exception;

class ResourceServiceException extends \Exception
{

    /** Internal message describing exception
     * @var string
     */
    public $message;

    public function __construct($message = "")
    {
        $this->message = $message;
    }

} 