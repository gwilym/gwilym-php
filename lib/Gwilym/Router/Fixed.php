<?php

class Gwilym_Router_Fixed extends Gwilym_Router
{
	protected $_routes = array();

	/**
	 * Add a new URI => Controller route to this Fixed router.
	 *
	 * @param string $uri
	 * @param Gwilym_Controller $controller
	 * @return Gwilym_Router_Fixed
	 */
	public function addFixedRoute ($uri, $controller)
	{
		$this->_routes[$uri] = $controller;
		return $this;
	}

	/**
	 * Given a Route consisting of a Controller and various arguments, this method will return a URI which will
	 * resolve to that controller. This is essentially the opposite of getRouteForRequest. This method always
	 * returns false for this Fixed router since URIs are arbitrarily specified instead of generated.
	 *
	 * @param Gwilym_Route $route
	 * @return string or false
	 */
	public function getUriForRoute (Gwilym_Route $route)
	{
		return false;
	}

	/**
	 * Given a Request, this method will return a Route pointing to a valid Controller or false if none could be
	 * resolved.
	 *
	 * @param Gwilym_Request $request
	 * @return Gwilym_Route or false
	 */
	public function getRouteForRequest (Gwilym_Request $request)
	{
		$uri = $request->getUri();
		foreach ($this->_routes as $routeUri => $controller) {
			if ($uri === $routeUri) {
				return new Gwilym_Route($request, $controller);
			}
		}
		return false;
	}
}
