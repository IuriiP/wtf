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
class Response extends \Wtf\Core\Entity implements \Wtf\Interfaces\Container, \Wtf\Interfaces\Bootstrap {

	use \Wtf\Traits\Container;

	/**
	 * @var array HTTP/1.1 response codes
	 */
	private static $_http = [
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

	/**
	 * @var int response code
	 */
	private $_code = 200;

	public $sent = false;

	/**
	 * Contracted name.
	 */
	public static function bootstrap() {
		App::contract('response', __CLASS__);
	}

	/**
	 * Set headers array.
	 * 
	 * @param array $array
	 * @return \Wtf\Core\Response Chainable
	 */
	public function headers($array = null) {
		if(!headers_sent()) {
			if(null === $array) {
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
	 * @return \Wtf\Core\Response Chainable
	 */
	public function code($code) {
		$this->_code = $code;
		return $this;
	}

	/**
	 * Define the required dependency.
	 * 
	 * @param mixed $content
	 * @param string $name 
	 * @param int $position
	 * @return \Wtf\Core\Response Chainable
	 */
	public function approve($content, $name = null, $position = 0) {
		$asset = [
			'position' => $position,
			'content' => $content,
		];
		if((\Wtf\Interfaces\Content::INJECT_HERE === $position) && !$this->hasChild($name)) {
			if(!$this->content) {
				$this->content($content);
			} else {
				$this->content->inject($asset);
			}
			if($name) {
				$this->child[$name] = null;
			}
		} elseif($name) {
			$this->child[$name] = $asset;
		} else {
			$this->child[] = $asset;
		}
		return $this;
	}

	/**
	 * Magic setter for any type injection.
	 * 
	 * EG: Code below
	 * ~~~
	 * $response->html()
	 *     ->style('.hidden {display:none;}')
	 *     ->script('$("#some").addClass("hidden");');
	 * ~~~
	 * will inject <style> and <script> tags into html.
	 * 
	 * @param type $type
	 * @param type $args
	 * @return \Wtf\Core\Response Chainable
	 */
	public function __call($type, $args) {
		if((count($args) < 1) || (null === $args[0])) {
			$this->approve(Entity::make($type, null));
		} elseif(!$this->content) {
			$content = Entity::factory($type, $args);
			if($content && ($content instanceof \Wtf\Interfaces\Content)) {
				$this->content($content);
			} else {
				trigger_error(__CLASS__ . "::{$type}: can't be content");
			}
		} elseif($this->content->canInject($type)) {
			switch(count($args)) {
				case 2: $this->approve(Entity::make($type, $args[1]), $args[0]);
					break;
				case 1: $this->approve(Entity::make($type, $args[0]));
					break;
				default: $this->approve(Entity::make($type, $args[1]), $args[0], $args[2]);
					break;
			}
		} else {
			trigger_error(__CLASS__ . "::{$type}: injecting not allowed to " . $this->content->getType());
		}
		return $this;
	}

	/**
	 * Clear content.
	 * 
	 * @return \Wtf\Core\Response Chainable
	 */
	public function clear() {
		$this->content = null;
		$this->children = [];
		return $this;
	}

	/**
	 * Immediately redirect.
	 * 
	 * @param type $url
	 * @param type $code
	 * @return \Wtf\Core\Response Chainable
	 */
	public function redirect($url, $code) {
		$this->clear()->header('Location', $url)->code($code? : 301);
		$this->sent = $this->send();
		return $this;
	}

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
					foreach($value as $subval) {
						header("{$key}: $subval", false);
					}
				} else {
					header("{$key}: $value", true);
				}
			}
		}
		return ($code >= 200) && ($code < 300);
	}

	/**
	 * Send headers and content.
	 * 
	 * @param array $trash Some trash information for including in debug purposes
	 * @return true
	 */
	public function send($trash = null) {
		if(!headers_sent() && $this->_sendHeader($this->_code) && $this->content) {
			header('Content-type: ' . $this->content->getMime(), true);
			if($trash) {
				if($this->content->isType('html')) {
					$this->approve(Entity::make('html_comment', $trash), '', \Wtf\Interfaces\Content::INJECT_END);
				} else {
					$this->approve(Entity::make('http_debug', $trash), '', \Wtf\Interfaces\Content::INJECT_HERE);
				}
			}
			if($this->children) {
				foreach(array_filter($this->children) as $asset) {
					$this->content->inject($asset);
				}
			}
			if($length = $this->content->getLength()) {
				header("Content-length: {$length}", true);
			}
			echo (string) $this->content;
		}
		return $this->sent = true;
	}

	/**
	 * Magic cast to string.
	 * 
	 * @return type
	 */
	public function __toString() {
		return (string) $this->content;
	}

}
