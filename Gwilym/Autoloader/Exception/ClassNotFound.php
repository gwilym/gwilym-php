<?php

class Gwilym_Autoloader_Exception_ClassNotFound extends Gwilym_Autoloader_Exception
{
	public function __construct ($className)
	{
		parent::__construct("Class '$className' not found.");
	}
}
