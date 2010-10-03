<?php

abstract class Gwilym_Router
{
	/** @var Gwilym_Route The route which is currently being followed from request to controller */
	protected $_currentRoute;
	
	/**
	* @param string $controller
	* @param string $action
	* @param array $args
	* @return string
	*/
	abstract public function getUriForRoute (Gwilym_Route $route);

	/**
	* @param string $uri
	* @return Gwilym_Route
	*/
	abstract public function getRouteForRequest (Gwilym_Request $request);

	public function defaultController ()
	{
		return 'Index';
	}

	public function routeRequest (Gwilym_Request $request)
	{
		$route = $this->getRouteForRequest($request);
		if (!$route) {
			return false;
		}
		$this->_currentRoute = $route;
		return $route->follow();
	}
	
	public function getCurrentRoute ()
	{
		return $this->_currentRoute;
	}
}
