<?php

class Gwilym_Request
{
	const MAX_NESTING_DEPTH = 20;

	/**
	* Calls to handle() may eventually make another call to handle() if transfer() is called. This value controls the maximum amount of times handle() can be called for one instance.
	*
	* @var int
	*/
	protected static $_nestingDepth = self::MAX_NESTING_DEPTH;

	protected $_routers = array();

	protected $_uriParser;

	public function __construct ($uri = null)
	{
		if ($uri !== null)
		{
			$this->uriParser(new Gwilym_UriParser_Fixed('', $uri));
		}
	}

	public function uriParser (Gwilym_UriParser $uriParser = null)
	{
		if ($uriParser !== null)
		{
			$this->_uriParser = $uriParser;
		}
		else if ($this->_uriParser === null)
		{
			$this->_uriParser = new Gwilym_UriParser_Guess;
		}
		return $this->_uriParser;
	}

	public function uri ()
	{
		return $this->uriParser()->uri();
	}

	public function addRouter ($router)
	{
		$this->_routers[] = $router;
	}

	public function handle ()
	{
		if (!self::$_nestingDepth--)
		{
			throw new Gwilym_Request_Exception_TooManyTransfers;
		}

		try
		{
			foreach ($this->_routers as $index => /** @var Gwilym_Router */$router)
			{
				if (is_string($router))
				{
					$router = $this->_routers[$index] = new $router;
				}

				if ($router->route($this) !== false) {
					return true;
				}
			}
		}
		catch (Gwilym_Request_Exception_Transferred $e)
		{
			// this exception is thrown by the transfer() method after a transfer, forcing a jump out of all other code paths at the point of call to ->transfer()
		}

		return false;
	}

	/**
	* Transfer current execution to another controller
	*
	* @param string $to Name of controller class to transfer to directly, or a URI to send through routing rules to discover new controller
	* @param array $args If $to is a class name, you can provide optional args, too
	*/
	public function transfer ($to, $args = array())
	{
		if (class_exists($to) && Gwilym_Reflection::isClassInstanciable($to))
		{
			$route = new Gwilym_Route($this, $to, $args);
			$route->follow();
		}
		else
		{
			$this->uri($to);
			$this->handle();
		}

		// this forces a jump out of all other code paths at the point of call to ->transfer()
		throw new Gwilym_Request_Exception_Transferred;
	}

	/**
	* @param string $controller
	* @param array $args
	* @return Gwilym_Route
	*/
	public function route ($controller, $args = array())
	{
		return new Gwilym_Route($this, $controller, $args);
	}

	public function routeToUri ($route)
	{
		foreach ($this->_routers as /** @var Gwilym_Router */$router)
		{
			if (is_string($router))
			{
				$router = $this->_routers[$index] = new $router;
			}

			$route = $router->routeToUri($route);
			if ($route)
			{
				return $route;
			}
		}
		return false;
	}
}
