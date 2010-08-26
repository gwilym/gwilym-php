<?php

class Controller_Index extends Gwilym_Controller
{
	public function action ()
	{
		$view = $this->view();
		$view->data['date'] = date('r');
	}
}
