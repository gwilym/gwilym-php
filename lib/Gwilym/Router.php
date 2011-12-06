<?php

/**
 * A Router's getRouteForRequest method is used by the Request class to produce a Route from the current Request to a
 * Controller.
 *
 * The getUriForRoute method produces the opposite result -- given a Route, a Router will produce a URI which will map
 * to that Route.
 */
abstract class Gwilym_Router
{
	/** @var Gwilym_Route The route which is currently being followed from request to controller */
	protected $_currentRoute;
	
	/**
	* Map the given Route to a Request using the rules of this Router.
	*
	* @param Gwilym_Route $route
	* @return Gwilym_Request
	*/
	abstract public function getRequestForRoute (Gwilym_Route $route);

	/**
	* Map the given Request to a Route using the rules of this Router.
	* 
	* @param Gwilym_Request $request
	* @return Gwilym_Route
	*/
	abstract public function getRouteForRequest (Gwilym_Request $request);

	/**
	 * The controller to use when no other can be successfully resolved.
	 */
	public function defaultController ()
	{
		return 'Index';
	}

	/**
	 * Attempts to find and follow a Route for the given Request.
	 *
	 * @return bool
	 */
	public function routeRequest (Gwilym_Request $request)
	{
		$route = $this->getRouteForRequest($request);
		if (!$route) {
			return false;
		}
		$this->_currentRoute = $route;
		$result = $route->follow();
		$this->_currentRoute = null;
		return $result;
	}
	
	/**
	 * Returns the route currently being followed by this request, if any.
	 *
	 * @return Gwilym_Route
	 */
	public function getCurrentRoute ()
	{
		return $this->_currentRoute;
	}
}
