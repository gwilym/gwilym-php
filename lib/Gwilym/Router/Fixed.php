<?php

class Gwilym_Router_Fixed extends Gwilym_Router
{
	protected $_routes = array();

	public function addFixedRoute ($uri, $controller)
	{
		$this->_routes[$uri] = $controller;
	}

	/**
	* @param string $controller
	* @param string $action
	* @param array $args
	* @return string
	*/
	public function routeToUri (Gwilym_Route $route)
	{
		return false;
	}

	/**
	* @param string $uri
	* @return Gwilym_Route
	*/
	public function requestToRoute (Gwilym_Request $request)
	{
		$requestUri = $request->getUri();
		foreach ($this->_routes as $routeUri => $controller) {
			if ($requestUri === $routeUri) {
				return new Gwilym_Route($request, $controller);
			}
		}
		return false;
	}
}
