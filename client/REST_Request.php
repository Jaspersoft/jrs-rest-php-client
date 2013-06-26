<?php

class REST_Request
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

	public function __construct ($url = null, $verb = 'GET', $request_body = null)
	{
		$this->url				= $url;
		$this->verb				= $verb;
		$this->request_body		= $request_body;
		$this->request_length	= 0;
		$this->username			= null;
		$this->password			= null;
		$this->accept_type		= null;
		$this->content_type		= 'application/xml';
		$this->response_body	= null;
		$this->response_info	= null;
		$this->file_to_upload	= array();

		if ($this->request_body !== null)
		{
			$this->buildPostBody();
		}
	}

	public function flush ()
	{
		$this->request_body		= null;
		$this->request_length	= 0;
		$this->verb				= 'GET';
		$this->response_body	= null;
		$this->response_info	= null;
		$this->content_type		= 'application/xml';
		$this->accept_type 		=  'application/xml';
		$this->file_to_upload	= null;
		}

	public function execute ()
	{
		$ch = curl_init();
		$this->setAuth($ch);

		try
		{
			switch (strtoupper($this->verb))
			{
				case 'GET':
					$this->executeGet($ch);
					break;
				case 'POST':
					$this->executePost($ch);
					break;
				case 'PUT':
					$this->executePut($ch);
					break;
				case 'DELETE':
					$this->executeDelete($ch);
					break;
				case 'PUT_MP':
					$this->verb = 'PUT';
					$this->executePutMultipart($ch);
					break;
				case 'POST_MP':
					$this->verb = 'POST';
					$this->executePostMultipart($ch);
					break;
				default:
					throw new \InvalidArgumentException('Current verb (' . $this->verb . ') is an invalid REST verb.');
			}
		}
		catch (\InvalidArgumentException $e)
		{
			curl_close($ch);
			throw $e;
		}
		catch (\Exception $e)
		{
			curl_close($ch);
			throw $e;
		}

	}

	public function buildPostBody ($data = null)
	{
		$data = ($data !== null) ? $data : $this->request_body;


		$this->request_body = $data;
	}

	protected function executeGet ($ch)
	{
		$this->doExecute($ch);
	}

	protected function executePost ($ch)
	{
		if (!is_string($this->request_body))
		{
			$this->buildPostBody();
		}

		curl_setopt($ch, CURLOPT_HTTPHEADER, array(
		'Content-Type: .' . $this->content_type
		));

		curl_setopt($ch, CURLOPT_POSTFIELDS, $this->request_body);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');

		$this->doExecute($ch);
	}

	// Set verb to PUT_MP to use this function
	protected function executePutMultipart ($ch)
	{
        $post = $this->request_body;

		curl_setopt($ch, CURLOPT_TIMEOUT, 10);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
		curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
		curl_setopt($ch, CURLOPT_URL, $this->url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

		$this->response_body = curl_exec($ch);
		$this->response_info	= curl_getinfo($ch);

		curl_close($ch);

	}
	// Set verb to POST_MP to use this function
	protected function executePostMultipart ($ch)
	{
		$post = $this->request_body;

		curl_setopt($ch, CURLOPT_TIMEOUT, 10);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
		curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
		curl_setopt($ch, CURLOPT_URL, $this->url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

		$this->response_body = curl_exec($ch);
		$this->response_info	= curl_getinfo($ch);

		curl_close($ch);

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
		$this->response_body = curl_exec($curlHandle);
		$this->response_info	= curl_getinfo($curlHandle);

		curl_close($curlHandle);
	}

	protected function setCurlOpts (&$curlHandle)
	{
		curl_setopt($curlHandle, CURLOPT_TIMEOUT, 10);
		curl_setopt($curlHandle, CURLOPT_URL, $this->url);
		curl_setopt($curlHandle, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curlHandle, CURLOPT_COOKIEFILE, '/dev/null');
        if (!empty($this->accept_type))
		    curl_setopt($curlHandle, CURLOPT_HTTPHEADER, array('Content-Type: ' . $this->content_type, 'Accept: ' . $this->accept_type));
        else
            curl_setopt($curlHandle, CURLOPT_HTTPHEADER, array('Content-Type: ' . $this->content_type));
	}

	protected function setAuth (&$curlHandle)
	{
		if ($this->username !== null && $this->password !== null)
		{
			curl_setopt($curlHandle, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
			curl_setopt($curlHandle, CURLOPT_USERPWD, $this->username . ':' . $this->password);
		}
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
}
