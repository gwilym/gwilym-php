<?php

interface Gwilym_Controller_Interface
{
	public function __construct (Gwilym_Request $request, $args);

	/**
	* Called before all controller actions. Override and return false to prevent action. If prevented, view will still display, so set appropriate view or set to Gwilym_View_None.
	*
	* @return bool
	*/
	public function before ();

	/**
	* To be defined by child classes implementing a croller.
	*
	* @param array $args arguments as provided by uri -> router pattern processing
	*/
	public function action ();

	/**
	* Called after all controller actions. Last chance to manipulate controller / view before view display.
	*
	* @return void
	*/
	public function after ();
}
