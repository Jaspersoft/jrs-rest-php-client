<?php
namespace Jaspersoft\Tool;

use Jaspersoft\Exception\RESTRequestException;

class RESTRequest
{

	protected $url;
	protected $verb;
	protected $request_body;
	protected $request_length;
	protected $username;
	protected $password;
	protected $accept_type;
	protected $content_type;
	protected $response_body;
	protected $response_info;
	protected $file_to_upload = array();
    protected $headers;
    protected $curl_timeout;
    protected $curl_handle;

	public function __construct ($url = null, $verb = 'GET', $request_body = null)
	{
		$this->url				= $url;
		$this->verb				= $verb;
		$this->request_body		= $request_body;
		$this->request_length	= 0;
		$this->username			= null;
		$this->password			= null;
		$this->accept_type		= null;
		$this->content_type		= 'application/json';
		$this->response_body	= null;
		$this->response_info	= null;
		$this->file_to_upload	= array();
        $this->curl_timeout     = 30;
        $this->curl_handle      = curl_init();
        $this->curl_cookiejar   = null;

        if ($this->request_body !== null)
		{
			$this->buildPostBody();
		}


	}

    public function __destruct() {
        // Clean up curl resources and delete cookie file remnants
        $this->closeCurlHandle(true);
    }

    /** This function will convert an indexed array of headers into an associative array where the key matches
     * the key of the headers, and the value matches the value of the header.
     *
     * This is useful to access headers by name that may be returned in the response from makeRequest.
     *
     * @param $array array Indexed header array returned by makeRequest
     * @return array
     */
    public $errorCode;
    public static function splitHeaderArray($array)
    {
        $result = array();
        foreach (array_values($array) as $value) {
            $pair = explode(':', $value, 2);
            if (count($pair) > 1) {
                $result[$pair[0]] = ltrim($pair[1]);
            } else {
                $result[] = $value;
            }
        }
        return $result;
    }

	protected function flush ()
	{
		$this->request_body		= null;
		$this->request_length	= 0;
		$this->verb				= 'GET';
		$this->response_body	= null;
		$this->response_info	= null;
		$this->content_type		= 'application/json';
		$this->accept_type 		= 'application/json';
		$this->file_to_upload	= null;
        $this->headers          = null;
        if (!is_resource($this->curl_handle)) {
            $this->curl_handle = curl_init();
        }
	}

	protected function execute ()
	{
		if (!is_resource($this->curl_handle)) {
            $this->curl_handle = curl_init();
        }
		$this->setAuth($this->curl_handle);
        $this->setTimeout($this->curl_handle);
		try
		{
			switch (strtoupper($this->verb))
			{
				case 'GET':
					$this->executeGet($this->curl_handle);
					break;
				case 'POST':
					$this->executePost($this->curl_handle);
					break;
				case 'PUT':
					$this->executePut($this->curl_handle);
					break;
				case 'DELETE':
					$this->executeDelete($this->curl_handle);
					break;
				case 'PUT_MP':
					$this->verb = 'PUT';
					$this->executePutMultipart($this->curl_handle);
					break;
                case 'POST_MP':
                    $this->verb = 'POST';
                    $this->executePostMultipart($this->curl_handle);
                    break;
                case 'POST_BIN':
                    $this->verb = 'POST';
                    $this->executeBinarySend($this->curl_handle);
                    break;
                case 'PUT_BIN':
                    $this->verb = 'PUT';
                    $this->executeBinarySend($this->curl_handle);
                    break;
                case 'PATCH':
                    $this->executePatch($this->curl_handle);
                    break;
				default:
					throw new \InvalidArgumentException('Current verb (' . $this->verb . ') is an invalid REST verb.');
			}
		}
		catch (\InvalidArgumentException $e)
		{
			$this->closeCurlHandle();
			throw $e;
		}
		catch (\Exception $e)
		{
            $this->closeCurlHandle();
			throw $e;
		}

	}

	protected function buildPostBody ($data = null)
	{
		$data = ($data !== null) ? $data : $this->request_body;
		$this->request_body = $data;
	}

	protected function executeGet ($ch)
	{
		$this->doExecute($ch);
	}

    protected function executePatch ($ch)
    {
        if (!is_string($this->request_body))
        {
            $this->buildPostBody();
        }
        curl_setopt($ch, CURLOPT_POSTFIELDS, $this->request_body);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $this->verb);

        $this->doExecute($ch);
    }

	protected function executePost ($ch)
	{
		if (!is_string($this->request_body))
		{
			$this->buildPostBody();
		}
		curl_setopt($ch, CURLOPT_POSTFIELDS, $this->request_body);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');

		$this->doExecute($ch);
	}

    protected function executeBinarySend ($ch)
    {
        $post = $this->request_body;

        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $this->verb);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
        curl_setopt($ch, CURLOPT_URL, $this->url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $this->headers);

        $this->response_body = curl_exec($ch);
        $this->response_info = curl_getinfo($ch);

        $this->closeCurlHandle();

    }

	// Set verb to PUT_MP to use this function
	protected function executePutMultipart ($ch)
	{
        $post = $this->request_body;

		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
		curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
		curl_setopt($ch, CURLOPT_URL, $this->url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

		$this->response_body = curl_exec($ch);
		$this->response_info = curl_getinfo($ch);

        $this->closeCurlHandle();

	}
	// Set verb to POST_MP to use this function
	protected function executePostMultipart ($ch)
	{
		$post = $this->request_body;

		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
		curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
		curl_setopt($ch, CURLOPT_URL, $this->url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

		$this->response_body = curl_exec($ch);
		$this->response_info	= curl_getinfo($ch);

        $this->closeCurlHandle();

	}
	protected function executePut ($ch)
	{

		if (!is_string($this->request_body))
		{
			$this->buildPostBody();
		}

		curl_setopt($ch, CURLOPT_POSTFIELDS, $this->request_body);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');

		$this->doExecute($ch);
	}

	protected function executeDelete ($ch)
	{
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');

		$this->doExecute($ch);
	}

	protected function doExecute (&$curlHandle)
	{
		$this->setCurlOpts($curlHandle);
        $response = curl_exec($curlHandle);
        $this->response_info = curl_getinfo($curlHandle);

        $response = preg_replace("/^(?:HTTP\/1.1 100 Continue.*?\\r\\n\\r\\n)+/ms", "", $response);

        //  100-continue chunks are returned on multipart communications
        $headerblock = strstr($response, "\r\n\r\n", true);

        // strstr returns the matched characters and following characters, but we want to discard of "\r\n\r\n", so
        // we delete the first 4 bytes of the returned string.
        $this->response_body = substr(strstr($response, "\r\n\r\n"), 4);
        // headers are always separated by \n until the end of the header block which is separated by \r\n\r\n.
        $this->response_headers = explode("\r\n", $headerblock);

        $this->closeCurlHandle();
	}

	protected function setCurlOpts (&$curlHandle)
	{
		curl_setopt($curlHandle, CURLOPT_URL, $this->url);
		curl_setopt($curlHandle, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curlHandle, CURLOPT_HEADER, true);

        if (!empty($this->content_type))
            $this->headers[] = "Content-Type: " . $this->content_type;
        if (!empty($this->accept_type))
		    $this->headers[] = "Accept: " . $this->accept_type;
        if (!empty($this->headers))
            curl_setopt($curlHandle, CURLOPT_HTTPHEADER, $this->headers);
	}

	protected function setAuth (&$curlHandle)
	{
		if ($this->username !== null && $this->password !== null)
		{
            if (empty($this->curl_cookiejar)) {
                $this->curl_cookiejar = tempnam(sys_get_temp_dir(), "jrscookies_");
            }
			curl_setopt($curlHandle, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
			curl_setopt($curlHandle, CURLOPT_USERPWD, $this->username . ':' . $this->password);
            curl_setopt($curlHandle, CURLOPT_COOKIESESSION, false);
            curl_setopt($curlHandle, CURLOPT_COOKIEJAR, $this->curl_cookiejar);   // we can keep cookies in temp dir
            curl_setopt($curlHandle, CURLOPT_COOKIEFILE, $this->curl_cookiejar); // until curl_close is called by logout.
		}
	}

    protected function setTimeout(&$curlHandle)
    {
        curl_setopt($curlHandle, CURLOPT_TIMEOUT, $this->curl_timeout);
    }
    public function defineTimeout($seconds)
    {
        $this->curl_timeout = $seconds;

    }
	public function getFileToUpload()
	{
		return $this->file_to_upload;
	}
	public function setFileToUpload($filepath)
	{
		$this->file_to_upload = $filepath;
	}
	public function getAcceptType ()
	{
		return $this->accept_type;
	}
	public function setAcceptType ($accept_type)
	{
		$this->accept_type = $accept_type;
	}
	public function getContentType ()
	{
		return $this->content_type;
	}
	public function setContentType ($content_type)
	{
		$this->content_type = $content_type;
	}
	public function getPassword ()
	{
		return $this->password;
	}
	public function setPassword ($password)
	{
		$this->password = $password;
	}
	public function getResponseBody ()
	{
		return $this->response_body;
	}
	public function getResponseInfo ()
	{
		return $this->response_info;
	}
	public function getUrl ()
	{
		return $this->url;
	}
	public function setUrl ($url)
	{
		$this->url = $url;
	}
	public function getUsername ()
	{
		return $this->username;
	}
	public function setUsername ($username)
	{
		$this->username = $username;
	}
	public function getVerb ()
	{
		return $this->verb;
	}
	public function setVerb ($verb)
	{
		$this->verb = $verb;
	}

    public function closeCurlHandle($close_cookies = false) {
        if (is_resource($this->curl_handle)) {
            curl_close($this->curl_handle);
        }

        if ($close_cookies && is_string($this->curl_cookiejar)) {
            unlink($this->curl_cookiejar);
            $this->curl_cookiejar = null;
        }
    }

    protected function handleError($statusCode, $expectedCodes, $responseBody)
    {
            if(!empty($responseBody)) {
                $errorData = json_decode($responseBody);
                $exception = new RESTRequestException(
                        (empty($errorData->message)) ? RESTRequestException::UNEXPECTED_CODE_MSG : $errorData->message
                );
                $exception->expectedStatusCodes = $expectedCodes;
                $exception->statusCode = $statusCode;
                if (!empty($errorData->errorCode)) {
                    $exception->errorCode = $errorData->errorCode;
                }
                if (!empty($errorData->parameters)) {
                    $exception->parameters = $errorData->parameters;
                }

                throw $exception;
            } else {
                $exception = new RESTRequestException(RESTRequestException::UNEXPECTED_CODE_MSG);
                $exception->expectedStatusCodes = $expectedCodes;
                $exception->statusCode = $statusCode;

                throw $exception;
            }
    }

    public function makeRequest($url, $expectedCodes = array(200), $verb = null, $reqBody = null, $returnData = false,
                                   $contentType = 'application/json', $acceptType = 'application/json', $headers = array())
    {
        $this->flush();
        $this->setUrl($url);
        if ($verb !== null) {
            $this->setVerb($verb);
        }
        if ($reqBody !== null) {
            $this->buildPostBody($reqBody);
        }
        if (!empty($contentType)) {
            $this->setContentType($contentType);
        }
        if(!empty($acceptType)) {
            $this->setAcceptType($acceptType);
        }
        if (!empty($headers))
            $this->headers = $headers;

        $this->execute();

        $info = $this->getResponseInfo();
        $statusCode = $info['http_code'];
        $body = $this->getResponseBody();

        $headers = $this->response_headers;

        // An exception is thrown here if the expected code does not match the status code in the response
        if (!in_array($statusCode, $expectedCodes)) {
            $this->handleError($statusCode, $expectedCodes, $body);
        }
 
        return compact("body", "statusCode", "headers");
    }

    public function prepAndSend($url, $expectedCodes = array(200), $verb = null, $reqBody = null, $returnData = false,
                                   $contentType = 'application/json', $acceptType = 'application/json', $headers = array())
    {
        $this->flush();
        $this->setUrl($url);
        if ($verb !== null) {
            $this->setVerb($verb);
        }
        if ($reqBody !== null) {
            $this->buildPostBody($reqBody);
        }
        if (!empty($contentType)) {
            $this->setContentType($contentType);
        }
        if(!empty($acceptType)) {
            $this->setAcceptType($acceptType);
        }
        if (!empty($headers))
            $this->headers = $headers;

        $this->execute();
        $statusCode = $this->getResponseInfo();
        $responseBody = $this->getResponseBody();
        $statusCode = $statusCode['http_code'];

        if (!in_array($statusCode, $expectedCodes)) {
            $this->handleError($statusCode, $expectedCodes, $responseBody);
        }

        if($returnData == true) {
            return $this->getResponseBody();
        }
        return true;
    }

    /**
     * This function creates a multipart/form-data request and sends it to the server.
     * this function should only be used when a file is to be sent with a request (PUT/POST).
     *
     * @param string $url - URL to send request to
     * @param int|string $expectedCode - HTTP Status Code you expect to receive on success
     * @param string $verb - HTTP Verb to send with request
     * @param string $reqBody - The body of the request if necessary
     * @param array $file - An array with the URI string representing the image, and the filepath to the image. (i.e: array('/images/JRLogo', '/home/user/jasper.jpg') )
     * @param bool $returnData - whether or not you wish to receive the data returned by the server or not
     * @return array - Returns an array with the response info and the response body, since the server sends a 100 request, it is hard to validate the success of the request
     */
    public function multipartRequestSend($url, $expectedCode = 200, $verb = 'PUT_MP', $reqBody = null, $file = null,
                                            $returnData = false)
    {
        $expectedCode = (integer) $expectedCode;
        $this->flush();
        $this->setUrl($url);
        $this->setVerb($verb);
        if (!empty($reqBody)) {
            $this->buildPostBody($reqBody);
        }
        if (!empty($file)) {
            $this->setFileToUpload($file);
        }
        $this->execute();
        $response = $this->getResponseInfo();
        $responseBody = $this->getResponseBody();
        $statusCode = $response['http_code'];

        return array($statusCode, $responseBody);
    }


    public function sendBinary($url, $expectedCodes = array(200), $body, $contentType, $contentDisposition, $contentDescription, $verb = "POST")
    {
        $this->flush();
        $this->setUrl($url);
        $this->setVerb($verb . '_BIN');
        $this->buildPostBody($body);
        $this->setContentType($contentType);
        $this->headers = array('Content-Type: ' . $contentType, 'Content-Disposition: ' . $contentDisposition, 'Content-Description: ' . $contentDescription, 'Accept: application/json');

        $this->execute();

        $statusCode = $this->getResponseInfo();
        $responseBody = $this->getResponseBody();
        $statusCode = $statusCode['http_code'];

        if (!in_array($statusCode, $expectedCodes)) {
            $this->handleError($statusCode, $expectedCodes, $responseBody);
        }
        return $this->getResponseBody();
    }
}
