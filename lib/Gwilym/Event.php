<?php

/**
 * This class implements an Event bind and trigger system within PHP with support for persisting event bindings between page calls via the Gwilym_KeyStore class.
 *
 * Event triggers can either be global or isolated to a specific instance of any PHP object (requires PHP 5.2.0+ for spl_object_hash()).
 *
 * Event bindings can be any valid PHP callback (such as functions, static public methods, instance public methods or closures in PHP 5.3.0+). However, only functions and static public method bindings can be persisted, and bindings for instance-specific events will also not be persisted.
 */
class Gwilym_Event
{
	/**
	* storage for bindings for an event, in the format of event_id => callback
	*
	* @var array<callback>
	*/
	protected static $_bindings = array();

	/**
	* storage for whether an event has had persistent bindings loaded yet, in the format of event_id => bool
	*
	* @var array<bool>
	*/
	protected static $_loaded = array();

	/**
	* flushes all in-memory bindings, but does not affect persisted bindings - generally used for tests only to clear memory and force a load from persisted bindings
	*
	* @return void
	*/
	protected static function _flushBindings ()
	{
		self::$_bindings = array();
		self::$_loaded = array();
	}

	/**
	* load any persisted bindings for a specific event, calling this on the same event name several times will only load once
	*
	* @param string $event
	* @return void
	*/
	protected static function _load ($event)
	{
		if (isset(self::$_loaded[$event]) && self::$_loaded[$event]) {
			return;
		}

		$bindings = Gwilym_KeyStore::multiGet('Gwilym_Event,' . $event . ',bind,*');

		foreach ($bindings as $binding)
		{
			if (strpos($binding, '::') === false)
			{
				// function callback
				self::$_bindings[$event][] = $binding;
			}
			else
			{
				// class::static callback
				$binding = explode('::', $binding);
				self::$_bindings[$event][] = array($binding[0], $binding[1]);
			}
		}

		self::$_loaded[$event] = true;
	}

	/**
	* bind a callback to an event with optional persistence between page loads
	*
	* @param object $object optional, the event can be object specific - binding to a specific object implies $persist = false as internal object ids are not unique between page loads
	* @param string $event event name
	* @param callback $callback callback, which can be a closure, an array(class, public static method), an array(object, public method), or a function name - binding a closure implies $persist = false as closures cannot be serialized
	* @param bool $persist optional, if true the binding will persist beyond this script execution (default false)
	* @throws Gwilym_Event_Exception_CannotPersistClosureBinding if an attempt is made to persist a closure as a callback
	* @throws Gwilym_Event_Exception_CannotPersistInstanceEvent if an attempt is made to persist a binding to an instance-specific event
	* @throws Gwilym_Event_Exception_CannotPersistInstanceBinding if an attempt is made to persist an instance-specific binding
	*/
	public static function bind ($object, $event, $callback = null, $persist = null)
	{
		$args = func_get_args();

		if (!is_object($object)) {
			$object = null;
			$event = array_shift($args);
			$callback = array_shift($args);
			$persist = (bool)array_shift($args);
		}

		if ($object) {
			$event = spl_object_hash($object) . '#' . $event;
		}

		self::$_bindings[$event][] = $callback;

		if (!$persist) {
			return true;
		}

		if (Gwilym_Reflection::isClosure($callback)) {
			throw new Gwilym_Event_Exception_CannotPersistClosureBinding;
		}

		if ($object) {
			throw new Gwilym_Event_Exception_CannotPersistInstanceEvent;
		}

		if (is_array($callback)) {
			if (is_object($callback[0])) {
				throw new Gwilym_Event_Exception_CannotPersistInstanceBinding;
			}

			// store array callbacks as strings to undo later when loading bindings
			$callback = $callback[0] . '::' . $callback[1];
		}

		Gwilym_KeyStore::set('Gwilym_Event,' . $event . ',bind,' . md5($callback), $callback);
	}

	/**
	* unbinds a callback from an event, including any persisted bindings
	*
	* @param object $object optional, the event can be object specific - binding to a specific object implies $persist = false as internal object ids are not unique between page loads
	* @param string $event event name
	* @param callback $callback callback, which can be a closure, an array(class, static method) or a function name - binding a closure implies $persist = false as closures cannot be serialized
	* @return void
	*/
	public static function unbind ($object, $event, $callback = null)
	{
		$args = func_get_args();
		$persist = true;

		if (!is_object($object)) {
			$object = null;
			$event = array_shift($args);
			$callback = array_shift($args);
		}

		if ($object) {
			$event = spl_object_hash($object) . '#' . $event;
			$persist = false; // cannot persist instance events so don't bother trying to delete them
		}

		if (Gwilym_Reflection::isClosure($callback)) {
			$persist = false; // cannot persist closure bindings so don't bother trying to delete them
		}

		if (is_array($callback) && is_object($callback[0])) {
			$persist = false; // cannot persist instance bindings so don't bother trying to delete them
		}

		if (isset(self::$_bindings[$event])) {
			foreach (self::$_bindings[$event] as $binding) {
				if ($binding === $callback) {
					unset(self::$_bindings[$event]);
				}
			}
		}

		if (!$persist) {
			// stop here if the type of event binding we're trying to unbind cannot be persisted
			return;
		}

		// delete persisted bindings
		if (is_array($callback)) {
			$callback = $callback[0] . '::' . $callback[1];
		}

		Gwilym_KeyStore::delete('Gwilym_Event,' . $event . ',bind,' . md5($callback));
	}

	/**
	* trigger an event
	*
	* @param object $object optional
	* @param string $event
	* @param mixed $data optional
	* @return Gwilym_Event
	*/
	public static function trigger ($object, $event = null, $data = null)
	{
		$args = func_get_args();

		if (is_object($object))
		{
			// triggering instance-specific event
			$key = spl_object_hash($object) . '#' . $event;
		}
		else
		{
			// triggering global event
			$data = $event;
			$event = $object;
			$key = $event;
			$object = null;
			self::_load($event);
		}

		$instance = new self($event);
		$instance->data = $data;

		if (!isset(self::$_bindings[$key]))
		{
			return $instance;
		}

		foreach (self::$_bindings[$key] as $binding)
		{
			$result = call_user_func($binding, $instance);

			if ($result === false)
			{
				$instance->stopPropagation();
				$instance->preventDefault();
				break;
			}

			if ($instance->isPropagationStopped())
			{
				break;
			}
		}

		return $instance;
	}

	// ====================

	protected $_defaultPrevented = false;

	public function isDefaultPrevented ()
	{
		return $this->_defaultPrevented;
	}

	public function preventDefault ()
	{
		$this->_defaultPrevented = true;
	}

	protected $_propagationStopped = false;

	public function isPropagationStopped ()
	{
		return $this->_propagationStopped;
	}

	public function stopPropagation ()
	{
		$this->_propagationStopped = true;
	}

	protected $_type;

	public function type ()
	{
		return $this->_type;
	}

	public function __construct ($type)
	{
		$this->_type = $type;
	}

	/** @var mixed data payload provided by trigger() */
	public $data = null;
}
