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

namespace Wtf\Traits;

/**
 * Collection functionality for Wtf\Interfaces\Collection
 * 
 * Supports access:
 * - Iterator
 * - $collection[$key] : \ArrayAccess
 * - $collection($key) : __invoke
 * - $collection->$key : __get, __set
 * - $collection->$key() : __call
 *
 * @author Iurii Prudius <hardwork.mouse@gmail.com>
 */
trait Collection {

	protected $_collection = [];

	/**
	 * Parse the complex offset by '/'.
	 * 
	 * @param string $offset
	 * @return array|null
	 */
	protected static function _parseComplex($offset) {
		if(is_string($offset) && (FALSE !== strpos($offset, '/'))) {
			return explode('/', $offset);
		}
		return null;
	}

	/**
	 * Check if the path exists.
	 * 
	 * @param \ArrayAccess $collection
	 * @param array $param
	 * @return bool
	 */
	protected static function _complexCheck($collection, $param) {
		$offset = strtolower(array_shift($param));
		if(isset($collection[$offset])) {
			if($param) {
				return self::_complexCheck($collection[$offset], $param);
			}
			return true;
		}
		return false;
	}

	/**
	 * Get a value from the path.
	 * 
	 * @param \ArrayAccess $collection
	 * @param array $param
	 * @return mixed
	 */
	protected static function _complexGet($collection, $param) {
		$offset = strtolower(array_shift($param));
		if(isset($collection[$offset])) {
			return $param ? self::_complexGet($collection[$offset], $param) : $collection[$offset];
		}
		return null;
	}

	/**
	 * Set the value by the path.
	 * 
	 * @param \ArrayAccess $collection
	 * @param array $param
	 * @param mixed $value
	 * @return mixed
	 */
	protected static function _complexSet(&$collection, $param, $value) {
		$offset = strtolower(array_shift($param));
		if($param) {
			if(!isset($collection[$offset])) {
				$collection[$offset] = [];
			}
			return self::_complexSet($collection[$offset], $param, $value);
		}
		return $collection[$offset] = $value;
	}

	/**
	 * Unset by the path.
	 * 
	 * @param \ArrayAccess $collection
	 * @param array $param
	 */
	protected static function _complexUnset(&$collection, $param) {
		$offset = strtolower(array_shift($param));
		if($param) {
			if(isset($collection[$offset])) {
				self::_complexUnset($collection[$offset], $param);
			}
			return;
		}
		unset($collection[$offset]);
	}

	/**
	 * Check if the offset exists.
	 * 
	 * @param string $offset
	 * @return bool
	 */
	public function offsetExists($offset) {
		$complex = self::_parseComplex($offset);
		if($complex) {
			return self::_complexCheck($this->_collection, $complex);
		}
		return isset($this->_collection[strtolower($offset)]);
	}

	/**
	 * Get a value by the offset.
	 * 
	 * @param string $offset
	 * @return mixed
	 */
	public function offsetGet($offset) {
		$complex = self::_parseComplex($offset);
		if($complex) {
			return self::_complexGet($this->_collection, $complex);
		}
		return self::_complexGet($this->_collection, [$offset]);
	}

	/**
	 * Set the value by the offset.
	 * 
	 * @param string $offset
	 * @param mixed $value
	 * @return mixed
	 */
	public function offsetSet($offset, $value) {
		$complex = self::_parseComplex($offset);
		if($complex) {
			return self::_complexSet($this->_collection, $complex, $value);
		}
		return $this->_collection[strtolower($offset)] = $value;
	}

	/**
	 * Unset by the offset.
	 * 
	 * @param string $offset
	 */
	public function offsetUnset($offset) {
		$complex = self::_parseComplex($offset);
		if($complex) {
			self::_complexUnset($this->_collection, $complex);
		} else {
			unset($this->_collection[strtolower($offset)]);
		}
	}

	/**
	 * Get the collection iterator.
	 * 
	 * @return \ArrayIterator
	 */
	public function getIterator() {
		return new \ArrayIterator($this->_collection);
	}

	/**
	 * Get and unset by the offset. Returns default if not exists.
	 * 
	 * @param string $offset
	 * @param mixed $def
	 * @return mixed
	 */
	public function eliminate($offset, $def = null) {
		$elem = $this->get($offset, $def);
		$this->offsetUnset($offset);
		return $elem;
	}

	/**
	 * Get a value by the offset. Returns default if not exists.
	 * 
	 * @param string $offset
	 * @param mixed $def
	 * @return mixed
	 */
	public function get($offset, $def = null) {
		return $this->offsetExists($offset) ? $this->offsetGet($offset) : $def;
	}

	/**
	 * Set the initial collection tree.
	 * 
	 * @param array $array
	 * @return \ArrayAccess
	 */
	public function set($array=[]) {
		$this->_collection = (array)$array;
		return $this->_collection;
	}

	/**
	 * Set the initial collection tree.
	 * 
	 * @param array $array
	 * @return \ArrayAccess
	 */
	public function mirror(&$array) {
		$this->_collection = &$array;
		return $this->_collection;
	}

	/**
	 * Magic getter.
	 * 
	 * @param string $offset
	 * @return mixed
	 */
	public function __get($offset) {
		return $this->offsetExists($offset) ? $this->offsetGet($offset) : null;
	}

	/**
	 * Magic setter.
	 * 
	 * @param string $offset
	 * @param mixed $value
	 * @return mixed
	 */
	public function __set($offset, $value) {
		return $this->offsetSet($offset, $value);
	}

	/**
	 * Hook for expanding a complex parameter.
	 * 
	 * @param array $args
	 * @return array
	 */
	static private function _expandArgs(array $args) {
		$reargs = [];
		foreach($args as $value) {
			if(false !== strpos($value, '/')) {
				$parts = explode('/', $value);
				$reargs = array_merge($reargs, $parts);
			} else {
				$reargs[] = $value;
			}
		}
		return $reargs;
	}

	/**
	 * Magic invoking.
	 * 
	 * $collection = new Collection();
	 * var_dump($collection('long/path',$part,'long/tail'));
	 * 
	 * @return mixed
	 */
	public function __invoke() {
		$args = self::_expandArgs(func_get_args());
		if(count($args)) {
			$offset = array_shift($args);
			return $this->__call($offset, $args);
		}
		return $this;
	}

	/**
	 * Magic calling.
	 * 
	 * $collection = new Collection();
	 * var_dump($collection->long('path',$part,'long/tail'));
	 * 
	 * @param string $offset
	 * @param array $args
	 * @return mixed
	 */
	public function __call($offset, $args = []) {
		if(($elem = $this->__get($offset)) && count($args)) {
			$args = $this->_expandArgs($args);
			if(is_object($elem)) {
				$obj = new \ReflectionObject($elem);
				if($obj->hasMethod('__invoke')) {
					$ref = $obj->getMethod('__invoke');
					return $ref->invokeArgs($elem, $args);
				}
			} elseif(is_callable($elem)) {
				return call_user_func_array($elem, $args);
			} elseif(is_array($elem) || ($elem instanceof \ArrayAccess)) {
				return self::_complexGet($elem, $args);
			}
			return null;
		}
		return $elem;
	}

	/**
	 * Magic static for singletons.
	 * 
	 * @param string $offset
	 * @param array $args
	 * @return mixed
	 * @throws Exception If the called class isn't singleton.
	 */
	public static function __callStatic($offset, $args = []) {
		$class = get_called_class();
		if(!is_subclass_of($class, 'Wtf\\Interfaces\\Singleton')) {
			throw new Exception("{$class}::Collection: static calling accepted by Singleton only.", E_ERROR);
		}
		return call_user_func_array([static::singleton(), $offset], $args);
	}

}
