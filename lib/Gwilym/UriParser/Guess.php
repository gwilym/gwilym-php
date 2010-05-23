<?php

/**
* This class implements a UriParser guessing scheme calculated off the location of the bootstrap file in the current URI.
*
* This is unnecessarily expensive to do on each request, but should work in most circumstances. On a predictable production server, it's recommended that the instance of this in the Gwilym_Request class is replaced with an instance of Gwilym_UriParser_Fixed instead.
*/
class Gwilym_UriParser_Guess extends Gwilym_UriParser
{
	protected $_parsed = false;
	protected $_base;
	protected $_docroot;
	protected $_uri;
	protected $_requestUri;
	protected $_requestBaseDir;

	public function requestUri ($requestUri = null)
	{
		if (func_num_args())
		{
			$this->_requestUri = $requestUri;
			$this->_parsed = false;
		}

		if ($this->_requestUri === null)
		{
			$this->_requestUri = $_SERVER['REQUEST_URI'];
		}

		return $this->_requestUri;
	}

	public function requestBaseDir ($requestBaseDir = null)
	{
		if (func_num_args())
		{
			$this->_requestBaseDir = $requestBaseDir;
			$this->_parsed = false;
		}

		if ($this->_requestBaseDir === null)
		{
			$this->_requestBaseDir = GWILYM_BASE_DIR;
		}

		return $this->_requestBaseDir;
	}

	protected function _parse ()
	{
		if ($this->_parsed)
		{
			return;
		}

		// try and find an alignment between the request URI which could be /sub/dir/friendly/url/ where our bootstrap file is located at /foo/bar/httpdocs/sub/dir/bootstrap.php and the relative uri request is /friendly/url/
		// the following code finds the common alignment of "/sub/dir/" in the full uri and the location of the bootstrap and determines that /foo/bar/httpdocs/ must be the root, /sub/dir/ is the sub-dir we're in, and /friendly/url/ is the framework content which is being requested
		// this code seems necessary on setups and servers where a reliable doc root env var is not available (such as IIS, or Apache setups using /~user/ directories)
		$base = explode('/', ltrim(str_replace('\\', '/', $this->requestBaseDir()), '/'));
		$uri = explode('/', ltrim($this->requestUri(), '/'));

		// in hindsight, there is probably a quicker way of doing this
		$j = min(array(count($base), count($uri)));
		for ($i = 1; $i <= $j; $i++) {
			$basepart = array_slice($base, count($base) - $i, $i);
			$subdir = array_slice($uri, 0, $i);
			if ($basepart == $subdir) {
				// match - found an alignment of the directories that the bootstrap file is in, compared to the uri requested
				$this->_base = '/' . implode('/', $subdir);
				$this->_docroot = implode(DIRECTORY_SEPARATOR, array_slice($base, 0, count($base) - $i));
				$this->_uri = '/' . implode('/', array_slice($uri, $i));
				$this->_parsed = true;
				return;
			}
		}

		// if no alignment is made, assume that bootstrap is located at the doc root, meaning there is no sub-dir and the REQUEST_URI is the actual uri we want
		$this->_docroot = $this->requestBaseDir();
		$this->_base = '';
		$this->_uri = $this->requestUri();
	}

	public function base ()
	{
		$this->_parse();
		return $this->_base;
	}

	public function docroot ()
	{
		$this->_parse();
		return $this->_docroot;
	}

	public function uri ()
	{
		$this->_parse();
		return $this->_uri;
	}
}
