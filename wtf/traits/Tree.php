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
 * Tree functionality for Wtf\Interfaces\Tree
 * 
 * Supports access:
 * - Iterator
 * - $tree[$key] : \ArrayAccess
 * - $tree($key) : __invoke
 * - $tree->$key : __get, __set
 * - $tree->$key() : __call
 *
 * @author Iurii Prudius <hardwork.mouse@gmail.com>
 */
trait Tree {

	protected $_tree = [];

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
	 * @param \ArrayAccess $tree
	 * @param array $param
	 * @return bool
	 */
	protected static function _complexCheck($tree, $param) {
		$offset = strtolower(array_shift($param));
		if(isset($tree[$offset])) {
			if($param) {
				return self::_complexCheck($tree[$offset], $param);
			}
			return true;
		}
		return false;
	}

	/**
	 * Get a value from the path.
	 * 
	 * @param \ArrayAccess $tree
	 * @param array $param
	 * @return mixed
	 */
	protected static function _complexGet($tree, $param) {
		$offset = strtolower(array_shift($param));
		if(isset($tree[$offset])) {
			return $param ? self::_complexGet($tree[$offset], $param) : $tree[$offset];
		}
		return null;
	}

	/**
	 * Set the value by the path.
	 * 
	 * @param \ArrayAccess $tree
	 * @param array $param
	 * @param mixed $value
	 * @return mixed
	 */
	protected static function _complexSet(&$tree, $param, $value) {
		$offset = strtolower(array_shift($param));
		if($param) {
			if(!isset($tree[$offset])) {
				$tree[$offset] = [];
			}
			return self::_complexSet($tree[$offset], $param, $value);
		}
		return $tree[$offset] = $value;
	}

	/**
	 * Unset by the path.
	 * 
	 * @param \ArrayAccess $tree
	 * @param array $param
	 */
	protected static function _complexUnset(&$tree, $param) {
		$offset = strtolower(array_shift($param));
		if($param) {
			if(isset($tree[$offset])) {
				self::_complexUnset($tree[$offset], $param);
			}
			return;
		}
		unset($tree[$offset]);
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
			return self::_complexCheck($this->_tree, $complex);
		}
		return isset($this->_tree[strtolower($offset)]);
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
			return self::_complexGet($this->_tree, $complex);
		}
		return self::_complexGet($this->_tree, [$offset]);
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
			return self::_complexSet($this->_tree, $complex, $value);
		}
		return $this->_tree[strtolower($offset)] = $value;
	}

	/**
	 * Unset by the offset.
	 * 
	 * @param string $offset
	 */
	public function offsetUnset($offset) {
		$complex = self::_parseComplex($offset);
		if($complex) {
			self::_complexUnset($this->_tree, $complex);
		} else {
			unset($this->_tree[strtolower($offset)]);
		}
	}

	/**
	 * Get the tree iterator.
	 * 
	 * @return \ArrayIterator
	 */
	public function getIterator() {
		return new \ArrayIterator($this->_tree);
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
	 * Set the initial tree.
	 * 
	 * @param array $array
	 * @return \ArrayAccess
	 */
	public function set($array = []) {
		$this->_tree = (array) $array;
		return $this->_tree;
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
	 * $tree = new Tree();
	 * var_dump($tree('long/path',$part,'long/tail'));
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
	 * $tree = new Tree();
	 * var_dump($tree->long('path',$part,'long/tail'));
	 * 
	 * @param string $offset
	 * @param array $args
	 * @return mixed
	 */
	public function __call($offset, $args = []) {
		if(($elem = $this->__get($offset)) && count($args)) {
			$args = self::_expandArgs($args);
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
		$class = static::class;

		if(!is_subclass_of($class, 'Wtf\\Interfaces\\Singleton')) {
			throw new \ErrorException("{$class}::Tree: static calling accepted by Singleton only.");
		}
		return call_user_func_array([static::singleton(), $offset], $args);
	}

	public function __isset($name) {
		return $this->offsetExists($offset);
	}

	public function __unset($name) {
		$this->offsetUnset($offset);
	}

}
