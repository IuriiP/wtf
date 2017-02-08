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
 * Server provides the readonly access to $_SERVER data.
 *
 * @author Iurii Prudius <hardwork.mouse@gmail.com>
 */
class Server implements \Wtf\Interfaces\Singleton, \Wtf\Interfaces\Invokable, \Wtf\Interfaces\Getter, \ArrayAccess {

	use \Wtf\Traits\Singleton;

	private $_server = [];
	
	/**
	 * Gets copy of current $_SERVER
	 * and override 'request_method' with 
	 * magic '_method' field from request.
	 */
	private function __construct() {
		$this->_server = array_change_key_case($_SERVER);
		// override 'request_method' from 'magic' request field
		if(isset($_REQUEST['_method'])) {
			$this->_server['request_method'] = $_REQUEST['_method'];
		}
	}

	/**
	 * 
	 * @param type $name
	 * @return type
	 */
	public function __get($name) {
		return $this->offsetGet($name);
	}

	public function __isset($name) {
		return $this->offsetExists($name);
	}

	public function offsetExists($offset) {
		return isset($this->_server[strtolower($offset)]);
	}

	public function offsetGet($offset) {
		$name = strtolower($offset);
		return isset($this->_server[$name]) ? $this->_server[$name] : null;
	}

	public function offsetSet($offset, $value) {
		// readonly
	}

	public function offsetUnset($offset) {
		// readonly
	}

	public function __invoke($offset) {
		return $this->offsetGet($offset);
	}

}
