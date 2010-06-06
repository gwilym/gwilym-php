<?php

class Gwilym_Response
{
	const STATUS_OK = 200;
	const STATUS_MOVED_PERMANENTLY = 301;
	const STATUS_SEE_OTHER = 303;
	const STATUS_METHOD_NOT_ALLOWED = 405;

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

	public function status ($status)
	{
		header('HTTP/1.1 ' . $status, true, $status);
	}

	public function header ($header, $value, $replace = true)
	{
		header($header . ': ' . $value, $replace);
	}

	public function end ()
	{
		exit;
	}
}
