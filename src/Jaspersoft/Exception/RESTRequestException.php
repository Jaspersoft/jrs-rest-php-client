<?php
namespace Jaspersoft\Exception;

class RESTRequestException extends \Exception
{

    const UNEXPECTED_CODE_MSG = "An unexpected HTTP status code was returned by the server";

    /** Internal Error Message
     *
     * @var string
     */    
    public $message;

    /** Expected HTTP Status Codes
     *
     * @var array
     */
    public $expectedStatusCodes;

    /** HTTP Status Code Given
     *
     * @var int
     */
    public $statusCode;

    /** Message returned by JRS
     *
     * @var string
     */
    public $jrsMessage;

    /** Error Code returned by JRS
     *
     * @var string
     */
    public $errorCode;

    /** Parameters returned by JRS
     *
     * @var array
     */
    public $parameters;

    public function __construct($message)
    {
        $this->message = $message;
    }

}