<?php

abstract class Gwilym_Controller
{
	/**
	* Local access to view class for this controller.
	*
	* @var Gwilym_View
	*/
	protected $_view;

	/**
	* @var Gwilym_Request
	*/
	protected $_request;

	/**
	* @var Gwilym_Response
	*/
	protected $_response;

	abstract public function action ($args);

	public function __construct (Gwilym_Request $request)
	{
		$this->_request = $request;
	}

	public function request ()
	{
		return $this->_request;
	}

	public function response ()
	{
		return $this->_response;
	}

	/**
	* By default, set a Twig-powered view which maps from Controller_Foo_Bar to /app/View/Foo/Bar.tpl. * Individual or abstract controllers extending Gwilym_Controller can override this method to either set a new pattern or a single, specific template.
	*
	* @return Gwilym_View
	*/
	public function view (Gwilym_View $view = null)
	{
		if (func_num_args())
		{
			$this->_view = $view;
		}

		if ($this->_view === null)
		{
			$this->_view = str_replace('_', '/', str_replace('^Controller_', '', '^' . get_class($this)));

			//$this->view .= '.php';
			//$this->view = new Gwilym_View_Php($this->view);

			$this->_view .= '.tpl';
			$this->_view = new Gwilym_View_Twig($this->_view);
		}

		return $this->_view;
	}

	/**
	* Called before all controller actions. Override and return false to prevent action. If prevented, view will still display, so set appropriate view or set to Gwilym_View_None.
	*
	* @return bool
	*/
	public function before ()
	{
		return true;
	}

	/**
	* Called after all controller actions. Last chance to manipulate controller / view before view display.
	*
	* @return void
	*/
	public function after ()
	{
	}
}
