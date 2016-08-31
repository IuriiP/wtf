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
 * Trait for Tree.
 *
 * @author IuriiP <hardwork.mouse@gmail.com>
 */
trait Tree {

	/**
	 *
	 * @var array
	 */
	protected $_leaves = [];

	/**
	 *
	 * @var array
	 */
	protected $_branches = [];

	/**
	 * Check if tree is empty.
	 * 
	 * @param type $param
	 * @return type
	 */
	final public function isEmpty() {
		return !$this->_leaves && !$this->_branches;
	}

	/**
	 * Add the leaf to the path.
	 * 
	 * @param mixed $object
	 * @param string|array $path
	 */
	final public function add($object, $path = '') {
		$pathes = is_string($path) ? explode('/', $path) : (array) $path;
		$seek = array_shift($pathes);

		if($seek) {
			if(is_object($object) ? !isset($this->_leaves[spl_object_hash($object)]) : in_array($object, $this->_leaves)) {
				if(!isset($this->_branches[$seek])) {
					$this->_branches[$seek] = new static;
				}
				$this->_branches[$seek]->add($object, $pathes);
			}
		} else {
			$this->all(function($leaf) use ($object) {
				return $leaf !== $object;
			});
			if(is_object($object)) {
				$this->_leaves[spl_object_hash($object)] = $object;
			} elseif(!in_array($object, $this->_leaves)) {
				$this->_leaves[] = $object;
			}
		}

		return $this;
	}

	/**
	 * Remove the object/(s) from the path.
	 * 
	 * @param string|array $path
	 * @param mixed $object
	 */
	final public function remove($path, $object = null) {
		$pathes = is_string($path) ? explode('/', $path) : (array) $path;
		$seek = array_shift($pathes);

		if($seek) {
			if($this->_branches && isset($this->_branches[$seek])) {
				$this->_branches[$seek]->remove($pathes, $object);
				if($this->_branches[$seek]->isEmpty()) {
					unset($this->_branches[$seek]);
				}
			}
		} elseif(!is_null($object)) {
			$this->all(function($leaf) use($object) {
				return $leaf !== $object;
			});
		} else {
			// cut this branch (reset)
			$this->_leaves = [];
			$this->_branches = [];
		}

		return $this;
	}

	/**
	 * Get all leaves on the path.
	 * 
	 * @param string|array $path
	 * @return array
	 */
	final public function leaves($path) {
		$pathes = is_string($path) ? explode('/', $path) : (array) $path;
		$seek = array_shift($pathes);

		if($seek) {
			return isset($this->_branches[$seek]) ? $this->_branches[$seek]->leaves($pathes) : [];
		}

		return $this->_leaves;
	}

	/**
	 * Get all branches on the path.
	 * 
	 * @param string|array $path
	 * @return array
	 */
	final public function branches($path) {
		$pathes = is_string($path) ? explode('/', $path) : (array) $path;
		$seek = array_shift($pathes);

		if($seek) {
			return isset($this->_branches[$seek]) ? $this->_branches[$seek]->branches($pathes) : [];
		}

		return $this->_branches;
	}

	/**
	 * Perform the callback on each leaf until on the path.
	 * 
	 * @param Callable $callback function($leaf) returns FALSE for remove this leaf.
	 * @param string|array $path
	 * @return $this
	 */
	final public function each($callback, $path = '') {
		$pathes = is_string($path) ? explode('/', $path) : (array) $path;
		$seek = array_shift($pathes);

		// process leaves
		foreach($this->_leaves as $key => $leaf) {
			if(!$callback($leaf)) {
				unset($this->_leaves[$key]);
			}
		}

		// process branch
		if($seek && isset($this->_branches[$seek])) {
			$branch = $this->_branches[$seek]->each($callback, $pathes);
			if($branch->isEmpty()) {
				unset($this->_branches[$seek]);
			}
		}

		return $this;
	}

	/**
	 * Perform the callback on each leaf on the tree.
	 * 
	 * @param Callable $callback function($leaf) returns FALSE for remove this leaf.
	 * @return $this
	 */
	final public function all($callback) {
		// process leaves
		foreach($this->_leaves as $key => $leaf) {
			if(!$callback($leaf)) {
				unset($this->_leaves[$key]);
			}
		}

		// process branches
		foreach($this->_branches as $seek => $branch) {
			$branch->all($callback);
			if($branch->isEmpty()) {
				unset($this->_branches[$seek]);
			}
		}

		return $this;
	}

}
