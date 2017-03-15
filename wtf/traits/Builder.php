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
 * Implementation of Wtf\Interfaces\Builder.
 *
 * @author IuriiP <hardwork.mouse@gmail.com>
 */
trait Builder {

	use Creator;

	private $_bricks = [];

	/**
	 * Chainable setter.
	 * 
	 * @param string $name
	 * @param array $arguments
	 * @return Wtf\Interface\Builder
	 */
	public function __call($name, $arguments) {
		if(!isset($this->_bricks[$name])) {
			$this->_bricks[$name] = [];
		}
		$this->_bricks[$name] = array_unique(array_merge($this->_bricks[$name], $arguments));

		return $this;
	}

	/**
	 * Make instance and call setter.
	 * 
	 * @param type $name
	 * @param type $arguments
	 */
	public static function __callStatic($name, $arguments) {
		$obj = static::_();
		return $obj->$name(...$arguments);
	}

	/**
	 * Get the brick set by the name.
	 * 
	 * @param string $name
	 * @return array
	 */
	public function __get($name) {
		return isset($this->_bricks[$name]) ? $this->_bricks[$name] : [];
	}

	/**
	 * Add value to the brick set.
	 * 
	 * @param string $name
	 * @param mixed $value
	 */
	public function __set($name, $value) {
		if(!isset($this->_bricks[$name])) {
			$this->_bricks[$name] = [];
		}
		if(!in_array($value, $this->_bricks[$name])) {
			$this->_bricks[$name][] = $value;
		}
	}

	/**
	 * Check if the brick exists.
	 * 
	 * @param type $name
	 * @return type
	 */
	public function __isset($name) {
		return isset($this->_bricks[$name]);
	}

	/**
	 * Remove the brick.
	 * 
	 * @param type $name
	 */
	public function __unset($name) {
		unset($this->_bricks[$name]);
	}

	/**
	 * Filtered getting bricks by names.
	 * 
	 * @return type
	 */
	public function __invoke() {
		$param = func_get_args();
		return $param ? array_intersect_key($this->_bricks, array_flip((array) $param)) : $this->_bricks;
	}

}
