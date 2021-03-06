<?php

/*
PHP class - Gwilym_JavaScript_Compressor

Copyright (c) 2010 Gwilym Evans

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in
all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
THE SOFTWARE.
*/

class Gwilym_JavaScript_Compressor extends Gwilym_JavaScript_Parser
{
	protected $_originalOutput;
	protected $_output;
	protected $_outputOpener;

	protected $_previousToken  = array(self::T_UNKNOWN, ''); // dummy entry
	protected $_currentToken;
	protected $_nextToken;

	protected function _tokenFinalised ()
	{
		if (!$this->_nextToken) {
			$this->_nextToken = array($this->_token, $this->_buffer);
			return;
		}

		$this->_currentToken = $this->_nextToken;
		$this->_nextToken = array($this->_token, $this->_buffer);
		$this->_compressToken();
	}

	protected function _eof ()
	{
		// since we're buffering a token ahead, we won't get a tokenFinalised for the last _nextToken
		$this->_currentToken = $this->_nextToken;
		$this->_nextToken = null;
		$this->_compressToken();
	}

	protected function _compressToken ()
	{
		switch ($this->_currentToken[0]) {
			// tokens to completely ignore
			case self::T_COMMENT_ML:
			case self::T_COMMENT_SL:
				return;

			// tokens to conditionally ignore
			case self::T_SEMICOLON:
				if ($this->_previousToken[0] === self::T_SEMICOLON) {
					// ignore duplicate semicolons
					return;
				}

				if ($this->_nextToken === null) {
					// ignore semicolon at end of file
					return;
				}

				switch ($this->_nextToken[0]) {
					case self::T_CLOSE_BRACE:
						// ignore semicolons before closing }
						return;
				}
				break;

			case self::T_WHITESPACE:
				switch ($this->_nextToken[0]) {
					case self::T_IN:
					case self::T_INSTANCEOF:
						// maintain space before these
						break;

					default:
						switch ($this->_previousToken[0]) {
							case self::T_CASE:
							case self::T_DELETE:
							case self::T_ELSE:
							case self::T_EXPORT:
							case self::T_FUNCTION:
							case self::T_IMPORT:
							case self::T_IN:
							case self::T_INSTANCEOF:
							case self::T_NEW:
							case self::T_RETURN:
							case self::T_THROW:
							case self::T_TYPEOF:
							case self::T_VAR:
								// maintain space after these
								break;
							default:
								// otherwise drop all other whitespace
								return;
						}
				}
				// if whitespace is to be sent, reduce it to 1 space
				$this->_currentToken[1] = ' ';
				break;

			case self::T_NEWLINE:
				// for now, drop all newlines - assuming input js is well-formed
				return;
		}

//		fwrite($this->_output, '[' . $this->_currentToken[0] . ':' . $this->_currentToken[1] . ']');
		fwrite($this->_output, $this->_currentToken[1]);
		$this->_previousToken = $this->_currentToken;
	}

	public function __construct ($input, $output)
	{
		parent::__construct($input);
		$this->_originalOutput = $output;
	}

	public function start ()
	{
		if (!$this->_started) {
			if (is_resource($this->_originalOutput)) {
				$this->_output = $this->_originalOutput;
				$this->_outputOpener = false;
			} else {
				$this->_output = fopen($this->_originalOutput, 'w');
				$this->_outputOpener = true;
			}
		}

		parent::start();
	}

	public function stop ()
	{
		if ($this->_started) {
			if ($this->_outputOpener) {
				fclose($this->_output);
			}
			$this->_output = null;
		}

		parent::stop();
	}
}
