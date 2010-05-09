<?php

abstract class Gwilym_View
{
	public $data = array();

	abstract public function display ();

	abstract public function render ();
}
