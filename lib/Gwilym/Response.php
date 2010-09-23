<?php

class Gwilym_Response
{
	const STATUS_OK = 200;
	const STATUS_MOVED_PERMANENTLY = 301;
	const STATUS_SEE_OTHER = 303;
	const STATUS_METHOD_NOT_ALLOWED = 405;

	/** @var Gwilym_Request */
	protected $_request;

	public function __construct (Gwilym_Request $request)
	{
		$this->_request;
	}

	/**
	* Send response to browser to redirect to another url.
	*
	* @param string $to URL to instruct browser to redirect to
	* @param string $status http status code
	*/
	public function redirect ($to, $status = self::STATUS_SEE_OTHER)
	{
		header('Location: ' . $to, true, $status);
		$this->end();
	}

	/**
	 * @return Gwilym_Response
	 */
	public function status ($status)
	{
		header('HTTP/1.1 ' . $status, true, $status);
		return $this;
	}

	/**
	 * @return Gwilym_Response
	 */
	public function header ($header, $value, $replace = true)
	{
		header($header . ': ' . $value, $replace);
		return $this;
	}

	/**
	 * Terminates the current response and ends the PHP script
	 *
	 * @todo look into ending the response but not the script, or throw an exception to be caught by the router
	 *       so it can clean up properly instead of just calling exit()
	 */
	public function end ()
	{
		exit;
	}
}
