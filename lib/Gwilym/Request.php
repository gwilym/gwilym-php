<?php

/**
* Request handler.
*
* When provided with user-agent data and routers, can resolve a request to a route pointing to a controller and dispatch down that route.
*/
class Gwilym_Request
{
	/** @var int max allowed nesting (transfer) depth before Gwilym_Request_Exception_TooManyTransfers is thrown */
	const MAX_NESTING_DEPTH = 20;

	/**
	* Calls to handle() may eventually make another call to handle() if transfer() is called. This value controls the maximum amount of times handle() can be called for one instance.
	*
	* This value starts at MAX_NESTING_DEPTH and counts down to 0, at which time a Gwilym_Request_Exception_TooManyTransfers exception will be thrown.
	*
	* @var int
	*/
	protected $_nestingDepth = self::MAX_NESTING_DEPTH;

	/** @var array<Gwilym_Router> */
	protected $_routers = array();

	/** @var Gwilym_UriParser */
	protected $_uriParser;

	/** @var Gwilym_Response */
	protected $_response;
	
	/** @var Gwilym_Router The router which is currently directing the request to the controller */
	protected $_currentRouter;

	/** @var array<mixed> storage for original $_GET data */
	private $_get;

	/** @var array<mixed> storage for original $_POST data */
	private $_post;

	/** @var array<mixed> storage for original $_COOKIE data */
	private $_cookie;

	/** @var array<mixed> storage for original session data, note: this is handled by reference so Gwilym_Request's $_session is just a reference to $_SESSION */
	private $_session = null;

	/** @var array<mixed> storage for original $_SERVER data */
	private $_server;
	
	/**
	* Calling this ensures the session has been started
	*
	* @return bool true if session was started otherwise false if session was already started previously
	*/
	protected function _startSession ()
	{
		if ($this->_session === null) {
			session_start();
			$this->_session = &$_SESSION;

			if (!isset($this->_session['Gwilym_Session_Started'])) {
				$this->_session['Gwilym_Session_Started'] = time();
			}

			if (!isset($this->_session['Gwilym_Session_Random'])) {
				$this->_session['Gwilym_Session_Random'] = uniqid('', true);
			}

			return true;
		}
		return false;
	}

	/**
	* @param string $uri URI for this request, or leave as null to determine based on user-agent-supplied data
	* @param array $get GET fields for this request, or leave as null to use user-agent-supplied data
	* @param array $post POST fields for this request, or leave as null to use user-agent-supplied data
	* @param array $cookie COOKIE fields for this request, or leave as null to use user-agent-supplied data
	* @param array $session SESSION fields for this request (supplying this will prevent real session interaction), or leave as null to use a real PHP session
	* @param array $server SERVER fields for this request, or leave as null to use user-agent- & server-api-supplied data
	* @return Gwilym_Request
	*/
	public function __construct ($uri = null, $get = null, $post = null, $cookie = null, $session = null, $server = null)
	{
		if ($uri !== null) {
			$this->uriParser(new Gwilym_UriParser_Fixed('', $uri));
		}

		$this->_get = $get === null ? $_GET : $get;
		$this->_post = $post === null ? $_POST : $post;
		$this->_cookie = $cookie === null ? $_COOKIE : $cookie;
		$this->_session = $session;
		$this->_server = $server === null ? $_SERVER : $server;
	}

	/**
	* Set or get the UriParser to use for this Request
	*
	* @param Gwilym_UriParser $uriParser
	* @return Gwilym_UriParser
	*/
	public function uriParser (Gwilym_UriParser $uriParser = null)
	{
		if ($uriParser !== null) {
			$this->_uriParser = $uriParser;
			return $this;
		}

		if ($this->_uriParser === null) {
			$this->_uriParser = new Gwilym_UriParser_Guess;
		}

		return $this->_uriParser;
	}

	/**
	* Returns the URI which this Request is handling
	*
	* @return string
	*/
	public function uri ()
	{
		return $this->uriParser()->uri();
	}

	/**
	* Add a router to this Request
	*
	* @param string|Gwilym_Router $router either a string to lazy-load the router class, or an instance of Gwilym_Router
	*/
	public function addRouter ($router)
	{
		$this->_routers[] = $router;
	}
	
	public function currentRouter ()
	{
		return $this->_currentRouter;
	}
	
	public function currentRoute ()
	{
		return $this->getCurrentRouter()->getCurrentRoute();
	}

	/**
	* Returns the HTTP request method used for this request
	*
	* @return string
	*/
	public function method ()
	{
		return strtoupper($_SERVER['REQUEST_METHOD']);
	}

	/**
	* Handles this request; resolving it through routers and dispatching it to a controller
	*
	* @return bool true if the request was dispatched to a router, otherwise false
	*/
	public function handle ()
	{
		$transfer = null;

		// begin a mainline loop which continues until the request is resolved or ended
		while (true) {
			if (!$this->_nestingDepth--) {
				throw new Gwilym_Request_Exception_TooManyTransfers;
			}

			// a request can be jumped-out of using exceptions; begin listening for that here
			try {
				if ($transfer) {
					return $transfer->follow();
				} else {
					foreach ($this->_routers as $index => /** @var Gwilym_Router */$router) {
						if (is_string($router)) {
							$router = $this->_routers[$index] = new $router;
						}
						$this->_currentRouter = $router;
						$result = $router->route($this);
						if ($result !== false) {
							// routing was successful
							return true;
						}
					}
					break;
				}
			} catch (Gwilym_Request_Exception_Transfer $exception) {
				$transfer = null;

				if (class_exists($exception->to) && Gwilym_Reflection::isClassInstanciable($exception->to)) {
					// explicit transfer to named class
					$transfer = $this->route($exception->to(), $exception->args());
				} else {
					// the specified transfer should be routed like a uri
					$previousParser = $this->uriParser();
					$fixedParser = new Gwilym_UriParser_Fixed(
						$previousParser->base(),
						$exception->to(),
						$previousParser->docroot()
					);
					$this->uriParser($fixedParser);
				}
			}
		}

		// if this point was reached then the request wasn't able to be handled properly
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
		$exception = new Gwilym_Request_Exception_Transfer;
		$exception->to($to);
		$exception->args($args);
		throw $exception;
	}

	/**
	* Given a controller class name and a set of arguments, will return an instance of Gwilym_Route which can be used for various purposes (like producing a usable URI)
	*
	* @param string $controller
	* @param array $args
	* @return Gwilym_Route
	*/
	public function route ($controller, $args = array())
	{
		if (!is_array($args)) {
			$args = array($args);
		}
		return new Gwilym_Route($this, $controller, $args);
	}

	/**
	* Given a Route, returns a URI based on this Request's list of routers (if several routers are present, only the first usable URI will be returned)
	*
	* @param Gwilym_Route $route
	*/
	public function routeToUri (Gwilym_Route $route)
	{
		foreach ($this->_routers as $index => /** @var Gwilym_Router */$router) {
			if (is_string($router)) {
				$router = $this->_routers[$index] = new $router;
			}

			if ($uri = $router->routeToUri($route)) {
				return $this->uriParser()->base() . $uri;
			}
		}
		return false;
	}

	/**
	* Wrapper for original $_GET data. Read only.
	*
	* @param string|null $key null if specified key does not exist, otherwise returns original value as supplied by user agent (most likely a string)
	*/
	public function get ($key)
	{
		return isset($this->_get[$key]) ? $this->_get[$key] : null;
	}

	/**
	* Wrapper for original $_POST data. Read only.
	*
	* @param string|null $key null if specified key does not exist, otherwise returns original value as supplied by user agent (most likely a string)
	*/
	public function post ($key)
	{
		return isset($this->_post[$key]) ? $this->_post[$key] : null;
	}

	/**
	* Wrapper for original $_COOKIE data.
	*
	* @param string $name cookie to reference
	* @param string $value optionally set cookie $name to specified value, or null to delete -- path, domain, etc. parameters must match those set on original cookie to delete it
	* @return string value supplied by user-agent or false if specified key does not exist
	*/
	public function cookie ($key, $value = null, $expire = 0, $path = null, $domain = null, $secure = false, $httponly = false)
	{
		if (func_num_args() < 2) {
			// get
			return isset($this->_cookie[$key]) ? $this->_cookie[$key] : null;
		}
		
		if ($value === null) {
			// delete
			unset($this->_cookie[$key]);
			__gwilym_request_setcookie($key, '', time() - 3600, $path, $domain, $secure, $httponly);
			return $this;
		}
		
		// set
		$this->_cookie[$key] = $value;
		__gwilym_request_setcookie($key, $value, $expire, $path, $domain, $secure, $httponly);
		return $this;
	}

	/**
	* Get or set session data for the current request
	*
	* @param string $key
	* @param mixed $value
	* @return mixed returns previously set value or null if key does not exist
	*/
	public function session ($key, $value = null)
	{
		$this->_startSession();
		if (func_num_args() == 2) {
			$this->_session[$key] = $value;
			return $this;
		}
		return isset($this->_session[$key]) ? $this->_session[$key] : null;
	}

	public function sessionId ()
	{
		$this->_startSession();
		return session_id();
	}

	/** @var Gwilym_Response */
	public function response ()
	{
		if ($this->_response === null) {
			$this->_response = new Gwilym_Response($this);
		}
		return $this->_response;
	}

	/**
	* Wrapper for original $_SERVER data. Read only.
	*
	* @param string|null $key null if specified key does not exist, otherwise returns original value as supplied by user agent / server api (most likely a string)
	*/
	public function server ($key)
	{
		return isset($this->_server[$key]) ? $this->_server[$key] : null;
	}
}

// version-specific wrappers for this class

if (version_compare(PHP_VERSION, '5.2.0') >= 0) {
	// setcookie $httponly added in 5.2
	function __gwilym_request_setcookie ($key, $value, $expire, $path, $domain, $secure, $httponly)
	{
		return setcookie($key, $value, $expire, $path, $domain, $secure, $httponly);
	}
} else {
	function __gwilym_request_setcookie ($key, $value, $expire, $path, $domain, $secure, $httponly)
	{
		return setcookie($key, $value, $expire, $path, $domain, $secure);
	}
}
