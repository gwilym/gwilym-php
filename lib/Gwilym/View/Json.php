<?php

/**
* This class implements a view type specifically for returning a chunk of JSON-encoded data. All of $this->data will be sent to the user-agent as a JSON object.
*/
class Gwilym_View_Json extends Gwilym_View
{
	public function display ()
	{
		echo $this->render();
	}

	public function render ()
	{
		return json_encode($this->data);
	}
}
