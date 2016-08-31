<?php

/*
 * Copyright (C) 2016 IuriiP <hardwork.mouse@gmail.com>
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

namespace Wtf\Traits;

/**
 * Basic Builder pattern.
 *
 * @author IuriiP <hardwork.mouse@gmail.com>
 */
trait Builder {

	use Creator;

	private $_bricks = [];

	public function __call($name, $arguments) {
		switch(count($arguments)) {
			case 0:
				unset($this->_bricks[$name]);
				break;
			case 1:
				$this->_bricks[$name][] = $arguments[0];
				break;
			default:
				if(!isset($this->_bricks[$name])) {
					$this->_bricks[$name] = [];
				}
				$this->_bricks[$name] = array_merge($this->_bricks[$name], $arguments);
		}
		return $this;
	}

	/**
	 * Get the brick value by the name.
	 * 
	 * @param string $name
	 * @return mixed
	 */
	public function __get($name) {
		return isset($this->_bricks[$name]) ? $this->_bricks[$name] : null;
	}

	public function __set($name, $value) {
		$this->_bricks[$name][] = $value;
	}

	public function __isset($name) {
		return isset($this->_bricks[$name]);
	}

	public function __unset($name) {
		unset($this->_bricks[$name]);
	}

	public function __invoke($param = null) {
		return $param ? array_intersect_key($this->_bricks, array_flip((array) $param)) : $this->_bricks;
	}

	public function getIterator() {
		return new \ArrayIterator($this->_bricks);
	}

}
