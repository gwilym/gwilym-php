<?php

class Gwilym_ErrorHandler
{
	public static function handleError ($errorCode, $errorDescription, $errorFile = '', $errorLine = 0, $errorContext = array())
	{
		$exception = new Gwilym_Exception_PhpError($errorDescription, $errorCode);

		$exception->setFile($errorFile);
		$exception->setLine($errorLine);

		throw $exception;
	}
}
