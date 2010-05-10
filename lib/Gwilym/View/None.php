<?php

/**
* This class implements a dummy, blank view, incase an empty response body is ever required.
*/
class Gwilym_View_None extends Gwilym_View
{
	public function display ()
	{

	}

	public function render ()
	{
		return '';
	}
}
