<?php

class Gwilym_Exception_PhpError extends Gwilym_Exception
{
	public function setFile ($file)
	{
		$this->file = $file;
	}

	public function setLine ($line)
	{
		$this->line = $line;
	}
}
