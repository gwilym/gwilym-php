<?php

abstract class Gwilym_Controller implements Gwilym_Controller_Interface
{
	/**
	 * Storage for View implementation for this controller.
	 *
	 * Private. Child classes should use view() to access.
	 *
	 * @var Gwilym_View
	 */
	private $_view;

	/**
	 * The request object which is invoking this controller.
	 *
	 * @var Gwilym_Request
	 */
	protected $_request;

	/**
	 * Array of arguments which have been passed from the Request to this controller. Exact contents and structure
	 * will vary depending on the URI, Route and perhaps the Controller itself.
	 *
	 * Validate your usage of this - it should be treated as unsafe.
	 *
	 * @var array
	 */
	protected $_args;
	
	/**
	 * Data dictionary for use by view when rendering. Should contain any models or ad-hoc information for the view
	 * to render correctly.
	 *
	 * @var array
	 */
	protected $_data = array();
	
	/**
	 * @param Gwilym_Request $request
	 * @param array $args
	 * @return Gwilym_Controller
	 */
	public function __construct (Gwilym_Request $request, $args = array())
	{
		$this->_request = $request;
		$this->_args = $args;
	}

	/**
	* The request object which is invoking this controller.
	*
	* @return Gwilym_Request
	*/
	public function getRequest ()
	{
		return $this->_request;
	}

	/**
	* The Response object for this Controller. Typically the same as $this->request()->response()
	*
	* @return Gwilym_Response
	*/
	public function getResponse ()
	{
		return $this->_request->response();
	}

	/**
	 * Generates a path to a default View file based on the name of this controller.
	 *
	 * @todo come up with a better way of implementing Views in general, and default Views specifically
	 * @return string
	 */
	public function getDefaultViewPath ($ext = 'php')
	{
		return str_replace('_', '/', str_replace('^Controller_', '', '^' . get_class($this))) . '.' . $ext;
	}

	/**
	 * The View object for this controller. By default this will be a View_Php type which is named after the
	 * current controller (such as /app/View/Foo/Bar.php). Child classes can override this to provide either a new
	 * algorithm for automatically creating a view, or provide a specific view.
	 *
	 * @return Gwilym_View
	 */
	public function getView (Gwilym_View $view = null)
	{
		if ($this->_view === null)
		{
			$this->_view = new Gwilym_View_Php($this->getDefaultViewPath());
		}

		return $this->_view;
	}
	
	public function setView (Gwilym_View $view)
	{
		$this->_view = $view;
	}

	/**
	 * Called before all controller actions. Override and return false to prevent action. If prevented but not
	 * redirected, view will still display, so set appropriate view or set to Gwilym_View_None.
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
	
	public function getData ()
	{
		return $this->_data;
	}
}
