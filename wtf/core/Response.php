<?php

/*
 * Copyright (C) 2016 Iurii Prudius <hardwork.mouse@gmail.com>
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

namespace Wtf\Core;

/**
 * Chainable Response
 *
 * @author Iurii Prudius <hardwork.mouse@gmail.com>
 */
abstract class Response implements \Wtf\Interfaces\Factory {

	use \Wtf\Traits\Factory;

	/**
	 * @var array HTTP/1.1 response codes
	 */
	protected static $_http = [
		//1xx: Informational:
		100 => 'Continue',
		101 => 'Switching Protocols',
		102 => 'Processing',
		//2xx: Success:
		200 => 'OK',
		201 => 'Created',
		202 => 'Accepted',
		203 => 'Non-Authoritative Information',
		204 => 'No Content',
		205 => 'Reset Content',
		206 => 'Partial Content',
		207 => 'Multi-Status',
		226 => 'IM Used',
		//3xx: Redirection:
		300 => 'Multiple Choices',
		301 => 'Moved Permanently',
		302 => 'Moved Temporarily',
		303 => 'See Other',
		304 => 'Not Modified',
		305 => 'Use Proxy',
		307 => 'Temporary Redirect',
		//4xx: Client Error:
		400 => 'Bad Request',
		401 => 'Unauthorized',
		402 => 'Payment Required',
		403 => 'Forbidden',
		404 => 'Not Found',
		405 => 'Method Not Allowed',
		406 => 'Not Acceptable',
		407 => 'Proxy Authentication Required',
		408 => 'Request Timeout',
		409 => 'Conflict',
		410 => 'Gone',
		411 => 'Length Required',
		412 => 'Precondition Failed',
		413 => 'Request Entity Too Large',
		414 => 'Request-URI Too Large',
		415 => 'Unsupported Media Type',
		416 => 'Requested Range Not Satisfiable',
		417 => 'Expectation Failed',
		422 => 'Unprocessable Entity',
		423 => 'Locked',
		424 => 'Failed Dependency',
		425 => 'Unordered Collection',
		426 => 'Upgrade Required',
		428 => 'Precondition Required',
		429 => 'Too Many Requests',
		431 => 'Request Header Fields Too Large',
		434 => 'Requested host unavailable',
		449 => 'Retry With',
		451 => 'Unavailable For Legal Reasons',
		//5xx: Server Error:
		500 => 'Internal Server Error',
		501 => 'Not Implemented',
		502 => 'Bad Gateway',
		503 => 'Service Unavailable',
		504 => 'Gateway Timeout',
		505 => 'HTTP Version Not Supported',
		506 => 'Variant Also Negotiates',
		507 => 'Insufficient Storage',
		508 => 'Loop Detected',
		509 => 'Bandwidth Limit Exceeded',
		510 => 'Not Extended',
		511 => 'Network Authentication Required',
	];

	/**
	 * @var array of headers
	 */
	private $_headers = [];

	protected $_code = 200;

	public $sent = false;

//	abstract public function __construct();
	/**
	 * Set headers array.
	 * 
	 * @param array $array
	 * @return \Wtf\Core\Response Chainable
	 */
	public final function headers($array = null) {
		if(!headers_sent()) {
			if(is_null($array)) {
				$this->_headers = [];
			} else {
				$this->_headers = array_merge($this->_headers, (array) $array);
			}
		}
		return $this;
	}

	/**
	 * Set/replace the header.
	 * 
	 * @param string $name
	 * @param mixed $value
	 * @return \Wtf\Core\Response Chainable
	 */
	public function header($name, $value = null) {
		if(!headers_sent()) {
			$this->_headers[$name] = $value;
		}
		return $this;
	}

	/**
	 * Set response code.
	 * 
	 * @param int $code
	 * @return \Wtf\Core\Response
	 */
	public final function code($code) {
		$this->_code = $code;
		return $this;
	}

	/**
	 * Helper: Immediately redirect.
	 * 
	 * @param string $url
	 * @param int $code
	 * @return \Wtf\Core\Response Chainable
	 */
	public final function redirect($url, $code = 301) {
		$this
			->headers()
			->clear()
			->header('Location', $url)
			->code($code)
			->send();
		return $this;
	}

	/**
	 * Clear content.
	 * 
	 * @return \Wtf\Core\Response Chainable
	 */
	abstract public function clear();

	/**
	 * Send unsent specific headers 
	 * and sends or returns body.
	 */
	abstract public function __toString();

	/**
	 * Send prepared headers.
	 * 
	 * @param int $code
	 * @return boolean Is code in 2xx
	 */
	private function _sendHeader($code) {
		header("HTTP/1.1 {$code} " . self::$_http[$code], true);

		foreach($this->_headers as $key => $value) {
			if(null !== $value) {
				if(is_array($value)) {
					foreach($value as $subkey => $subval) {
						header("{$key}-{$subkey}: " . str_replace(["\n", "\t", "\r"], '', $subval), false);
					}
				} else {
					header("{$key}: " . str_replace(["\n", "\t", "\r"], '', $subval), true);
				}
			}
		}
	}

	/**
	 * Send headers and content.
	 * 
	 * @param array $trash Some trash information for including in debug purposes
	 * @return true
	 */
	public final function send($trash = null) {
		if($trash) {
			$this->headers(['X-Trash' => (array) $trash]);
		}

		$this->_sendHeader($this->_code);
		$out = (string) $this;

		// turn off output buffering
		while(FALSE !== ob_end_clean()) {
			
		}

		if($out) {
			echo $out;
		}

		return $this->sent = true;
	}

}
