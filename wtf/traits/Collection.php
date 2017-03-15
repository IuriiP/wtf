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
 * Implementation of \Wtf\Interfaces\Collection.
 *
 * @author IuriiP <hardwork.mouse@gmail.com>
 */
trait Collection {

	private $_collection = [];

	public function count() {
		return count($this->_collection);
	}

	public function add($object) {
		$this->_collection[] = $object;
		return $this;
	}

	public function current() {
		return current($this->_collection);
	}

	public function key() {
		return key($this->_collection);
	}

	public function next() {
		return next($this->_collection);
	}

	public function rewind() {
		reset($this->_collection);
		return true;
	}

	public function valid() {
		return !is_null(key($this->_collection));
	}

}
