<?php

abstract class Gwilym_View
{
	/**
	 * @var Gwilym_Controller
	 */
	protected $_controller;
	
	/**
	 * @return string
	 */
	abstract public function display ();

	/**
	 * @return void
	 */
	abstract public function render ();
	
	public function __construct (Gwilym_Controller $controller)
	{
		$this->_controller = $controller;
	}
	
	public function data ()
	{
		return $this->_controller->data();
	}
}
