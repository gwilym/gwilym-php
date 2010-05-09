<?php

class Gwilym_Request
{
	/**
	* Calls to handle() may eventually make another call to handle() if transfer() is called. This value controls the maximum amount of times handle() can be called for one instance.
	*
	* @var int
	*/
	protected static $_nestingDepth = 20;

	protected $_routers = array();

	protected $_uri = array();

	protected $_base = '/mongodm';

	public function __construct ($uri = null)
	{
		// hack: can you discover /mongodm/ is a sub-dir based off path of index.php vs. a doc-root setting somewhere?
		if ($uri === null)
		{
			// hack: stuffs up requests to ie. /blah/mongodm/
			$this->_uri = str_replace($this->_base, '', $_SERVER['REQUEST_URI']);
		}
		else
		{
			$this->_uri = $uri;
		}
	}

	public function base ()
	{
		return $this->_base;
	}

	public function uri ()
	{
		return $this->_uri;
	}

	/**
	* @return Gwilym_Router
	*/
	public function defaultRouter ()
	{
		return $this->_routers[0];
	}

	public function addRouter (Gwilym_Router $router)
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
			foreach ($this->_routers as /** @var Gwilym_Router */$router)
			{
				if ($router->route($this) !== false) {
					break;
				}
			}
		}
		catch (Gwilym_Request_Exception_Transferred $e)
		{
			// this exception is thrown by the transfer() method after a transfer, forcing a jump out of all other code paths at the point of call to ->transfer()
		}
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
}
