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
 * Description of Cookie
 *
 * @author Iurii Prudius <hardwork.mouse@gmail.com>
 */
class Cookie implements \Wtf\Interfaces\Singleton, \ArrayAccess {

	use \Wtf\Traits\Singleton;
	
	private $_array = [];

	private function __construct() {
		$this->_array = $_COOKIE;
	}

	private function _filterOffset($offset) {
		return preg_replace('/[=,; \t\r\n\013\014]+/', '_', $offset);
	}
	
	public function offsetExists($offset) {
		return isset($this->_array[$this->_filterOffset($offset)]);
	}

	public function offsetGet($offset) {
		$name = $this->_filterOffset($offset);
		return isset($this->_array[$name])?$this->_array[$name]:null;
	}

	public function offsetSet($offset, $value) {
		$name = $this->_filterOffset($offset);
		if(is_scalar($value)) {
			setcookie($name, $value);
			$this->_array[$name] = $value;
		} elseif(is_array($value)) {
			setcookie($name, 
				$this->_array[$name] = \Wtf\Helper\Complex::get($value, 'value', ''), 
				\Wtf\Helper\Complex::get($value, 'expires', 0), 
				\Wtf\Helper\Complex::get($value, 'path', '/'), 
				\Wtf\Helper\Complex::get($value, 'domain', ''), 
				\Wtf\Helper\Complex::get($value, 'secure', false), 
				\Wtf\Helper\Complex::get($value, 'httponly', false)
				);
		} else {
			throw new \Wtf\Exceptions\ArgumentsException(__CLASS__.'::offsetSet()');
		}
	}

	public function offsetUnset($offset) {
		$name = $this->_filterOffset($offset);
		setcookie($name);
		unset($this->_array[$name]);
	}

}
