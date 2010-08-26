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
	abstract public function routeToUri (Gwilym_Route $route);

	/**
	* @param string $uri
	* @return Gwilym_Route
	*/
	abstract public function requestToRoute (Gwilym_Request $request);

	public function defaultController ()
	{
		return 'Index';
	}

	public function route (Gwilym_Request $request)
	{
		$route = $this->requestToRoute($request);
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
