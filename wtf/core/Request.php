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

use Wtf\Core\App,
	Wtf\Core\Response;

/**
 * Description of Request
 *
 * @author Iurii Prudius <hardwork.mouse@gmail.com>
 */
class Request implements \Wtf\Interfaces\Bootstrap {

	private $_route = null;

	private $_map = null;

	private $_method = null;

	private $_input = null;

	public function __construct($route, $map = []) {
		$this->_route = $route? : '/';
		$this->_map = $map? : App::config('routes');
	}

	/**
	 * Process routes from list
	 * 
	 * @param string $method
	 * @param array $input
	 */
	public function execute($method = 'get', $input = []) {
		$this->_method = $method;
		$this->_input = $input;
		if($this->_map) {
			foreach($this->_map as $rule) {
				$ret = $this->_useRule($rule);
				if(FALSE !== $ret) {
					return $ret;
				}
			}
			return Response::error(404, ['route' => $this->_route]);
		}
		return Response::error(500, ['route' => $this->_route]);
	}

	/**
	 * Get value from $this->_input | $_POST | $_GET | $_COOKIE
	 * 
	 * @param string $name
	 * @param mixed $def
	 */
	final public function input($name, $def = null) {
		if(isset($this->_input[$name])) {
			return $this->_input[$name];
		}
		if(isset($_POST[$name])) {
			return $_POST[$name];
		}
		if(isset($_GET[$name])) {
			return $_GET[$name];
		}
		if(isset($_COOKIE[$name])) {
			return $_COOKIE[$name];
		}
		return $def;
	}

	/**
	 * Get value from array
	 * 
	 * @param string $name
	 * @param mixed $def
	 */
	final static public function extract($source, $name, $def) {
		$src = $source;
		$parts = explode('.', $name);
		foreach($parts as $sub) {
			if(isset($src[$sub])) {
				$src = $src[$sub];
			} else {
				return $def;
			}
		}
		return $src;
	}

	/**
	 * Get value from $_GET
	 * 
	 * @param string $name
	 * @param mixed $def
	 */
	final static public function get($name, $def = null) {
		return self::extract($_GET, $name, $def);
	}

	/**
	 * Get value from $_POST
	 * 
	 * @param string $name
	 * @param mixed $def
	 */
	final static public function post($name, $def = null) {
		return self::extract($_POST, $name, $def);
	}

	/**
	 * Get value from $_COOKIE
	 * 
	 * @param string $name
	 * @param mixed $def
	 */
	final static public function cookie($name, $def = null) {
		return self::extract($_COOKIE, $name, $def);
	}

	/**
	 * Get value from $_FILES
	 * 
	 * @param string $name
	 * @param mixed $def
	 */
	final static public function file($name, $def = null) {
		return self::extract($_FILES, $name, $def);
	}

	/**
	 * Check to the route matching 
	 * and try execute the matched closure.
	 * 
	 * @param string $method
	 * @param array $input
	 */
	private function _useRule($rule) {
		if($rule && ($pattern = $rule['pattern'])) {
			$matches = [];
			$method = $rule['method'];
			if((!$method || (FALSE !== stripos($this->_method, $method))) && preg_match("#^{$pattern}$#", $this->_route, $matches)) {
				$mapper = $rule['mapper'];
				$closure = $rule['closure'];
				if(is_string($closure)) {
					return $this->_callback($closure, array_combine($mapper, $matches));
				} elseif(is_object($closure)) {
					return $this->_closure($closure, array_combine($mapper, $matches));
				} elseif(is_array($closure)) {
					return $this->_routes($closure, array_combine($mapper, $matches));
				}
			}
		}
		return FALSE;
	}

	/**
	 * Process string
	 * 
	 * @param string $callback
	 * @param array $mapped
	 * @return \Wtf\Core\Response|FALSE
	 */
	private function _callback($callback, $mapped) {
		$callable = explode('::', preg_replace_callback('/{(\w+)}/', function($match) use($mapped) {
				return empty($mapped[$match[1]]) ? '' : $mapped[$match[1]];
			}, $callback));
		if(count($callable) < 2) {
			$callable[1] = $this->_method;
		}

		if($contract = App::get($callable[0])) {
			$callable[0] = $contract;
		}
		$callname = '';
		if(is_callable($callable, false, $callname)) {
			try {
				return call_user_func($callable, $mapped, $this->_method, $this->_input);
			} catch(Exception $exc) {
				trigger_error($exc->getMessage() . ' At ' . __CLASS__ . "::execute: calling '{$callname}' on route '$this->_route'");
			}
		}
		return FALSE;
	}

	/**
	 * Process lambda-function
	 * 
	 * @param \Closure $closure
	 * @param array $mapped
	 * @return \Wtf\Core\Response|FALSE
	 */
	private function _closure($closure, $mapped) {
		$callname = null;
		if(is_callable($closure, false, $callname)) {
			try {
				return call_user_func($closure, $mapped, $this->_method, $this->_input);
			} catch(Exception $exc) {
				trigger_error($exc->getMessage() . ' At ' . __CLASS__ . "::execute: calling '{$callname}' on route '$this->_route'");
			}
		}
		return FALSE;
	}

	/**
	 * Process nested sub-routes.
	 * Parsed variables from the parent route 
	 * pass in the overrided $input array.
	 * 
	 * @param array $array
	 * @param array $mapped
	 * @return \Wtf\Core\Response|FALSE
	 */
	private function _routes($array, $mapped) {
		$request = new Request($mapped['_'], $array);
		unset($mapped['']);
		unset($mapped['_']);
		return $request->execute($this->_method, array_replace_recursive($this->_input, $mapped));
	}

	public static function bootstrap(\Wtf\Core\App $app) {
		$app::contract('request', __CLASS__);
	}

}
