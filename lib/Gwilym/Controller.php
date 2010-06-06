<?php

abstract class Gwilym_Controller implements Gwilym_Controller_Interface
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

	protected $_args;

	/**
	* @param Gwilym_Request $request
	* @return Gwilym_Controller
	*/
	public function __construct (Gwilym_Request $request, $args = array())
	{
		$this->_request = $request;
		$this->_args = $args;
		$this->_response = new Gwilym_Response;
	}

	/**
	* The request object which is invoking this controller.
	*
	* @return Gwilym_Request
	*/
	public function request ()
	{
		return $this->_request;
	}

	/**
	* Response object for directly controlling output to the user-agent
	*
	* @return Gwilym_Response
	*/
	public function response ()
	{
		return $this->_response;
	}

	public function getDefaultViewPath ($ext = 'php')
	{
		return str_replace('_', '/', str_replace('^Controller_', '', '^' . get_class($this))) . '.' . $ext;
	}

	/**
	* By default, set a plain PHP view type which maps from Controller_Foo_Bar to /app/View/Foo/Bar.php. Individual or abstract controllers extending Gwilym_Controller can override this method to either set a new pattern or a single, specific template.
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
			$this->_view = new Gwilym_View_Php($this->getDefaultViewPath());
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
