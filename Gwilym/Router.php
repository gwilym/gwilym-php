<?php

abstract class Gwilym_Router
{
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
		if (!$route)
		{
			return false;
		}
		return $route->follow();
	}
}
