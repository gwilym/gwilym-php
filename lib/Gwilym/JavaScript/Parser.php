<?php

class Gwilym_JavaScript_Parser extends Gwilym_FSM_StreamParser
{
	const T_UNKNOWN = 0;
	const T_COMMENT_SL = 1;
	const T_COMMENT_ML = 2;
	const T_STRING_SQ = 3;
	const T_STRING_DQ = 4;
	const T_WHITESPACE = 5;
	const T_NEWLINE = 6;
	const T_OPEN_PAREN = 7;
	const T_CLOSE_PAREN = 8;
	const T_OPEN_BRACE = 9;
	const T_CLOSE_BRACE = 10;
	const T_IDENTIFIER = 11;
	const T_DOT = 12;
	const T_FUNCTION = 13;
	const T_OPER = 14;
	const T_FORWARD_SLASH = 15;
	const T_OPER_MODULUS = 16;
	const T_OPER_BW_XOR = 17;
	const T_OPER_BW_NOT = 18;
	const T_OPER_TERNARY = 19;
	const T_SEMICOLON = 20;
	const T_COMMA = 21;
	const T_VAR = 22;
	const T_COLON = 22;
	const T_OPEN_BRACKET = 23;
	const T_CLOSE_BRACKET = 24;
	const T_NUMBER_INT = 26;
	const T_NUMBER_FLOAT = 27;
	const T_REGEX = 28;
	const T_OPER_ASSIGN = 29;
	const T_OPER_EQ = 30;
	const T_OPER_EQ_STRICT = 31;
	const T_OPER_NEQ = 32;
	const T_OPER_NEQ_STRICT = 33;
	const T_OPER_NOT = 34;
	const T_OPER_LT = 35;
	const T_OPER_LTE = 36;
	const T_OPER_BW_LSHIFT = 37;
	const T_OPER_GT = 38;
	const T_OPER_GTE = 39;
	const T_OPER_BW_RSHIFT = 40;
	const T_OPER_BW_ZFRSHIFT = 41;
	const T_OPER_BW_OR = 42;
	const T_OPER_OR = 43;
	const T_OPER_ADDITION = 44;
	const T_OPER_INCREMENT = 45;
	const T_OPER_ASSIGN_ADDITION = 46;
	const T_OPER_SUBTRACT = 47;
	const T_OPER_DECREMENT = 48;
	const T_OPER_ASSIGN_DECREMENT = 49;
	const T_OPER_MULTIPLICATION = 50;
	const T_OPER_ASSIGN_MULTIPLICATION = 51;
	const T_OPER_DIVISION = 52;
	const T_OPER_ASSIGN_DIVISION = 53;
	const T_UNDEFINED = 54;
	const T_RETURN = 55;
	const T_NUMBER_INT_EXPONENTIAL = 56;
	const T_NUMBER_FLOAT_EXPONENTIAL = 57;
	const T_IF = 58;
	const T_ELSE = 59;
	const T_ELSE_IF = 60;
	const T_IN = 61;
	const T_TYPEOF = 62;
	const T_BREAK = 63;
	const T_CASE = 64;
	const T_CONTINUE = 65;
	const T_DEFAULT = 66;
	const T_DELETE = 67;
	const T_DO = 68;
	const T_EXPORT = 69;
	const T_COMMENT = 70;
	const T_FOR = 71;
	const T_IMPORT = 72;
	const T_LABEL = 73;
	const T_NEW = 74;
	const T_SWITCH = 75;
	const T_THIS = 76;
	const T_VOID = 77;
	const T_WHILE = 78;
	const T_WITH = 79;
	const T_CONST = 80;
	const T_TRY = 81;
	const T_CATCH = 82;
	const T_THROW = 83;
	const T_FINALLY = 84;
	const T_INSTANCEOF = 85;

	protected function _getStates ()
	{
		return array(
			'init' => 'read',
			'read' => array(
				array(
					'on' => 'isEOF',
					'goto' => 'eof',
				),
				'process',
			),
			'process' => array(
				array(
					'on' => 'isKnownToken',
					'goto' => 'processKnownToken',
				),
				'processUnknownToken',
			),
			'processKnownToken' => 'finishProcess',
			'processUnknownToken' => 'finishProcess',
			'finishProcess' => array(
				array(
					'on' => 'isEOT',
					'goto' => 'finaliseToken',
				),
				'read',
			),
			'finaliseToken' => 'read',
			'eof' => 'end',
			'end' => null,
		);
	}

	protected $_eof;
	protected $_eot;
	protected $_greedy;

	protected $_char;
	protected $_nextchar;
	protected $_buffer;
	protected $_token;

	/** @var array storage for a memory of tokens which have just been parsed, which assists with detected regex and arithmetic tokens based on preceeding tokens */
	protected $_statement;
	protected $_stack;
	protected $_push;
	protected $_flush;
	protected $_pop;

	/** @var bool flag which is set when the parser is in escape / control-character mode */
	protected $_escape;

	/** @var array a map for guessing unknown tokens based on first few characters */
	protected $_guesses = array(
		" "		=> self::T_WHITESPACE,
		"\t"	=> self::T_WHITESPACE,
		"\v"	=> self::T_WHITESPACE,
		"\f"	=> self::T_WHITESPACE,
		"/*"	=> self::T_COMMENT_ML,
		"//"	=> self::T_COMMENT_SL,
		"\r"	=> self::T_NEWLINE,
		"\n"	=> self::T_NEWLINE,
		"("		=> self::T_OPEN_PAREN,
		")"		=> self::T_CLOSE_PAREN,
		"{"		=> self::T_OPEN_BRACE,
		"}"		=> self::T_CLOSE_BRACE,
		"'"		=> self::T_STRING_SQ,
		'"'		=> self::T_STRING_DQ,
		"."		=> self::T_DOT,
		"="		=> self::T_OPER,
		"!"		=> self::T_OPER_NOT,
		"<"		=> self::T_OPER,
		">"		=> self::T_OPER,
		"|"		=> self::T_OPER,
		"&"		=> self::T_OPER,
		"+"		=> self::T_OPER,
		"-"		=> self::T_OPER,
		"*"		=> self::T_OPER,
		"%"		=> self::T_OPER_MODULUS,
		"^"		=> self::T_OPER_BW_XOR,
		"~"		=> self::T_OPER_BW_NOT,
		"?"		=> self::T_OPER_TERNARY,
		":"		=> self::T_COLON,
		","		=> self::T_COMMA,
		";"		=> self::T_SEMICOLON,
		"["		=> self::T_OPEN_BRACKET,
		"]"		=> self::T_CLOSE_BRACKET,
		"/"		=> self::T_FORWARD_SLASH,
	);

	/** @var array map of known operators to tokens */
	protected $_operMap = array(
		"="		=> self::T_OPER_ASSIGN,
		"=="	=> self::T_OPER_EQ,
		"==="	=> self::T_OPER_EQ_STRICT,
		"!="	=> self::T_OPER_NEQ,
		"!=="	=> self::T_OPER_NEQ_STRICT,
		"!"		=> self::T_OPER_NOT,
		"<"		=> self::T_OPER_LT,
		"<="	=> self::T_OPER_LTE,
		"<<"	=> self::T_OPER_BW_LSHIFT,
		">"		=> self::T_OPER_GT,
		">="	=> self::T_OPER_GTE,
		">>"	=> self::T_OPER_BW_RSHIFT,
		">>>"	=> self::T_OPER_BW_ZFRSHIFT,
		"|"		=> self::T_OPER_BW_OR,
		"||"	=> self::T_OPER_OR,
		"&"		=> self::T_OPER_BW_OR,
		"&&"	=> self::T_OPER_OR,
		"+"		=> self::T_OPER_ADDITION,
		"++"	=> self::T_OPER_INCREMENT,
		"+="	=> self::T_OPER_ASSIGN_ADDITION,
		"-"		=> self::T_OPER_SUBTRACT,
		"--"	=> self::T_OPER_DECREMENT,
		"-="	=> self::T_OPER_ASSIGN_DECREMENT,
		"*"		=> self::T_OPER_MULTIPLICATION,
		"*="	=> self::T_OPER_ASSIGN_MULTIPLICATION,
		"/"		=> self::T_OPER_DIVISION,
		"/="	=> self::T_OPER_ASSIGN_DIVISION,
	);

	/** @var array a list of tokens which are both single characters and cannot be shared with the start of other tokens, so don't need to be checked further */
	protected $_uniqueTokens = array(
		self::T_OPEN_BRACE => true,
		self::T_OPEN_BRACKET => true,
		self::T_OPEN_PAREN => true,
		self::T_CLOSE_BRACE => true,
		self::T_CLOSE_BRACKET => true,
		self::T_CLOSE_PAREN => true,
		self::T_DOT => true,
		self::T_COMMA => true,
		self::T_OPER_BW_NOT => true,
		self::T_OPER_BW_XOR => true,
		self::T_OPER_MODULUS => true,
		self::T_OPER_TERNARY => true,
		self::T_COLON => true,
		self::T_SEMICOLON => true,
	);

	/** @var array a list of tokens where their ending is defined by a known character sequence and so, when read in, that character is included in the token itself (such as ML comments always ending with * / so we include the / when we see it) */
	protected $_greedyTokens = array(
		self::T_OPER_DIVISION => true,
		self::T_COMMENT_ML => true,
		self::T_STRING_DQ => true,
		self::T_STRING_SQ => true,
		self::T_REGEX => true,
	);

	protected $_pushTokens = array(
		self::T_OPEN_BRACE => self::T_CLOSE_BRACE,
		self::T_OPEN_BRACKET => self::T_CLOSE_BRACKET,
		self::T_OPEN_PAREN => self::T_CLOSE_PAREN,
	);

	protected $_popTokens = array(
		self::T_CLOSE_BRACE => self::T_OPEN_BRACE,
		self::T_CLOSE_BRACKET => self::T_OPEN_BRACKET,
		self::T_CLOSE_PAREN => self::T_OPEN_PAREN,
	);

	/** @var array a listo f tokens that trigger a _statement data flush */
	protected $_flushTokens = array(
		self::T_SEMICOLON => true,
	);

	/** @var array a list of words that are picked up initially as T_IDENTIFIER but are actually other named tokens */
	protected $_keywords = array(
		'function' => self::T_FUNCTION,
		'var' => self::T_VAR,
		'undefined' => self::T_UNDEFINED,
		'return' => self::T_RETURN,
		'if' => self::T_IF,
		'else' => self::T_ELSE,
		'else if' => self::T_ELSE_IF,
		'in' => self::T_IN,
		'typeof' => self::T_TYPEOF,
		'break' => self::T_BREAK,
		'case' => self::T_CASE,
		'continue' => self::T_CONTINUE,
		'default' => self::T_DEFAULT,
		'delete' => self::T_DELETE,
		'do' => self::T_DO,
		'export' => self::T_EXPORT,
		'comment' => self::T_COMMENT,
		'for' => self::T_FOR,
		'import' => self::T_IMPORT,
		'label' => self::T_LABEL,
		'new' => self::T_NEW,
		'switch' => self::T_SWITCH,
		'this' => self::T_THIS,
		'void' => self::T_VOID,
		'while' => self::T_WHILE,
		'with' => self::T_WITH,
		'const' => self::T_CONST,
		'try' => self::T_TRY,
		'catch' => self::T_CATCH,
		'throw' => self::T_THROW,
		'finally' => self::T_FINALLY,
		'instanceof' => self::T_INSTANCEOF,
	);

	/** @var string list of valid, non-newline whitespace chars between real tokens (not inside strings) */
	protected $_whitespaceChars = " \t\v\f";

	/** @var string list of valid chars in identifiers (ignoring first-character non-numeric requirement) */
	protected $_identifierChars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ_$1234567890';

	/** @var string list of valid chars in operators */
	protected $_operChars = "=!<>|&+-/%^~*";

	protected function _init ()
	{
		$this->_nextchar = fgetc($this->_stream);
		$this->_eof = $this->_nextchar === false;
		$this->_buffer = '';
		$this->_token = self::T_UNKNOWN;
		$this->_statement = array();
		$this->_stack = array();
		$this->_flush = false;
		$this->_pop = false;
		$this->_push = false;
		$this->_escape = false;
	}

	protected function _isEOF ()
	{
		return $this->_eof;
	}

	protected function _isEOT ()
	{
		return $this->_eot;
	}

	protected function _isKnownToken ()
	{
		return $this->_token !== self::T_UNKNOWN;
	}

	protected function _read ()
	{
		if ($this->_eof) {
			return;
		}

		if (strlen($this->_char) === 0) {
			$this->_char = $this->_nextchar;
			$this->_nextchar = fgetc($this->_stream);
			$this->_buffer .= $this->_char;
			$this->_eof = $this->_char === false;
//			echo $this->_token . "< " . $this->_char . "\n";
		} else {
//			echo $this->_token . "^ " . $this->_char . "\n";
		}
	}

	protected function _processUnknownToken ()
	{
		$this->_eot = false;
		$this->_greedy = false;
		$this->_flush = false;
		$this->_pop = false;
		$this->_push = false;
		$this->_escape = false;

		if (isset($this->_guesses[$this->_buffer])) {
			// buffer matches a symbol in the initial-guess map
			$this->_token = $this->_guesses[$this->_buffer];
		} else if (!is_numeric($this->_char) && strpos($this->_identifierChars, $this->_char) !== false) {
			$this->_token = self::T_IDENTIFIER;
		} else if (is_numeric($this->_char)) {
			// could be a float but start off as an int and verify it later
			$this->_token = self::T_NUMBER_INT;
		} else {
			throw new Exception('Unexpected token: ' . $this->_buffer);
		}

		if (isset($this->_uniqueTokens[$this->_token])) {
			$this->_eot = true;
			$this->_greedy = true;
		} else if (isset($this->_greedyTokens[$this->_token])) {
			$this->_greedy = true;
		}
	}

	protected function _processKnownToken ()
	{
//		echo "?:" . $this->_token . "> " . substr($this->_buffer, 0, 20) . "\n";

		if ($this->_token === self::T_FORWARD_SLASH) {
			// we're at character 2+ of a / token, time to make a call on whether it's a...
			// - division operator
			// - division-assignment operator
			// - regular expression
			// - single line comment
			// - multiline comment

			if (isset($this->_guesses[$this->_buffer])) {
				// probably just a comment
				$this->_token = $this->_guesses[$this->_buffer];
			} else {
				// something else...
				for ($counter = count($this->_statement); $counter--;) {
					switch ($this->_statement[$counter][0]) {
						case self::T_WHITESPACE:
						case self::T_NEWLINE:
							// keep looking back
							break;

						case self::T_IDENTIFIER:
						case self::T_STRING_DQ:
						case self::T_STRING_SQ:
						case self::T_NUMBER_FLOAT:
						case self::T_NUMBER_INT:
						case self::T_CLOSE_PAREN:
							// most likely an operator
							$this->_token = self::T_OPER;
							break;

						default:
							// most likely a regex
							$this->_token = self::T_REGEX;
							break;
					}

					if ($this->_token !== self::T_FORWARD_SLASH) {
						break;
					}
				}
			}

			if ($this->_token === self::T_FORWARD_SLASH && $counter === -1) {
				// most likely a regex
				$this->_token = self::T_REGEX;
			}

			if ($this->_token === self::T_FORWARD_SLASH) {
				var_dump($this->_statement);
				throw new Exception("Don't know what to do with the forward-slash in this statement-in-progress");
			}
		}

		switch ($this->_token) {
			case self::T_COMMENT_ML:
				if ($this->_char === '/' && substr($this->_buffer, -2) === '*/') {
					$this->_eot = true;
				}
				break;

			case self::T_COMMENT_SL:
				if ($this->_char === "\n" || $this->_char === "\r") {
					$this->_eot = true;
				}
				break;

			case self::T_NEWLINE:
				if ($this->_char !== "\n" && $this->_char !== "\r") {
					$this->_eot = true;
				}
				break;

			case self::T_IDENTIFIER:
				if (strpos($this->_identifierChars, $this->_char) === false) {
					$this->_eot = true;
				}
				break;

			case self::T_STRING_DQ:
				if ($this->_escape) {
					// todo: multi-char escape codes
					$this->_escape = false;
				} else {
					if ($this->_char === '\\') {
						$this->_escape = true;
					} else if ($this->_char === '"') {
						$this->_eot = true;
					}
				}
				break;

			case self::T_STRING_SQ:
				if ($this->_escape) {
					// todo: multi-char escape codes
					$this->_escape = false;
				} else {
					if ($this->_char === '\\') {
						$this->_escape = true;
					} else if ($this->_char === "'") {
						$this->_eot = true;
					}
				}
				break;

			case self::T_WHITESPACE:
				if (strpos($this->_whitespaceChars, $this->_char) === false) {
					$this->_eot = true;
				}
				break;

			case self::T_OPER:
				// some kind of operator, for now, let's not bother validating and just bundle sets of operators into one
				if (strpos($this->_operChars, $this->_char) === false) {
					$this->_eot = true;
				}
				break;

			case self::T_NUMBER_INT:
				if ($this->_char === '.') {
					$this->_token = self::T_NUMBER_FLOAT;
					break;
				}
				if ($this->_char === 'e') {
					$this->_token = self::T_NUMBER_INT_EXPONENTIAL;
					break;
				}
				if (!is_numeric($this->_char)) {
					$this->_eot = true;
				}
				break;

			case self::T_REGEX:
				if ($this->_escape) {
					// todo: multi-char escape codes
					$this->_escape = false;
//						echo "REGEX ESCAPE OFF: " . $this->_char . "\n";
				} else {
					if ($this->_char === '\\') {
//						echo "REGEX ESCAPE ON: " . $this->_buffer . "\n";
						$this->_escape = true;
					} else if ($this->_char === '/') {
						$this->_eot = true;
					}
				}
				break;

			case self::T_NUMBER_FLOAT:
				if ($this->_char === 'e') {
					$this->_token = self::T_NUMBER_FLOAT_EXPONENTIAL;
					break;
				}
				if (!is_numeric($this->_char)) {
					$this->_eot = true;
				}
				break;

			case self::T_OPER_NOT:
				if ($this->_char === '=') {
					$this->_token = self::T_OPER_NEQ;
				} else {
					$this->_eot = true;
				}
				break;

			case self::T_OPER_NEQ:
				if ($this->_char === '=') {
					$this->_token = self::T_OPER_NEQ_STRICT;
					$this->_greedy = true;
				}
				$this->_eot = true;
				break;

			default:
				throw new Exception('No processing implemented for token ' . $this->_token);
				break;
		}
	}

	protected function _finishProcess ()
	{
		$this->_char = null;
	}

	protected function _finaliseToken ()
	{
		if (!$this->_greedy) {
			$this->_char = substr($this->_buffer, -1);
			$this->_buffer = substr($this->_buffer, 0, strlen($this->_buffer) - 1);
		}

		// token has ended, last chance to transform to a different token based on the buffer
		switch ($this->_token) {
			case self::T_IDENTIFIER:
				if (isset($this->_keywords[$this->_buffer])) {
					$this->_token = $this->_keywords[$this->_buffer];
					break;
				}
				break;

			case self::T_OPER:
				if (!isset($this->_operMap[$this->_buffer])) {
					throw new Exception('Unexpected or invalid operator: ' . $this->_buffer);
				}
				$this->_token = $this->_operMap[$this->_buffer];
				break;
		}

		if (!$this->_greedy && isset($this->_greedyTokens[$this->_token])) {
			// transformation above has changed over to a greedy token, so reclaim the char we shedded above
			$this->_greedy = true;
			$this->_buffer .= $this->_char;
			$this->_char = null;
		}

//		echo "EOT" . ($this->_greedy ? "G" : "") . ":" . $this->_token . "> " . $this->_buffer . "\n";

		$this->_tokenFinalised();

		// post processing of the token - handling of the stack, etc.

		if (isset($this->_pushTokens[$this->_token])) {
			$this->_push = true;
		}

		if (isset($this->_popTokens[$this->_token])) {
			$this->_pop = true;
		}

		if (isset($this->_flushTokens[$this->_token])) {
			$this->_flush = true;
		}

		if ($this->_push && $this->_pop) {
			throw new Exception('Both stack push and pop are set for token ' . $this->_token . ': ' . $this->_buffer);
		}

		if ($this->_push) {
//			echo "PUSH  " . $this->_buffer . "\n";
			$this->_statement[] = array($this->_token, $this->_buffer);
			array_push($this->_stack, $this->_statement);
			$this->_statement = array();
		}

		if ($this->_flush) {
//			echo "FLUSH " . $this->_buffer . "\n";
			$this->_statement[] = array($this->_token, $this->_buffer);
			$this->_statementFlushed();
			$this->_statement = array();
		}

		if ($this->_pop) {
			$this->_statement = array_pop($this->_stack);
			$this->_statement[] = array($this->_token, $this->_buffer);
//			echo "POP   " . $this->_buffer . "\n";
		}

		if (!$this->_push && !$this->_flush && !$this->_pop) {
			$this->_statement[] = array($this->_token, $this->_buffer);
		}

		// remove the current token to start over
		$this->_token = self::T_UNKNOWN;

		if ($this->_greedy) {
			$this->_buffer = '';
		} else {
			$this->_buffer = $this->_char;
		}
	}

	protected function tokToString ($tokens)
	{
		foreach ($tokens as $token) {
			echo $token[1];
		}
	}

	protected function _tokenFinalised ()
	{
		// reserved for extendees
	}

	protected function _statementFlushed ()
	{
		// reserved for extendees
	}

	protected function _eof ()
	{
		if (!empty($this->_stack)) {
			throw new Exception('Unexpected EOF');
		}

		switch ($this->_token) {
			case self::T_UNKNOWN:
			case self::T_WHITESPACE:
			case self::T_NEWLINE:
				break;

			default:
				throw new Exception('Unexpected EOF');
		}
	}
}
