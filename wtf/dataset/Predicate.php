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

namespace Wtf\Dataset;

/**
 * Description of Predicate
 *
 * @author IuriiP <hardwork.mouse@gmail.com>
 */
class Predicate implements \IteratorAggregate, \Wtf\Interfaces\Creator {

	use \Wtf\Traits\Creator;

	private $_fn = null;

	private $_args = null;

	public function __construct($fn, $args = []) {
		$this->_fn = (string) $fn;
		$this->_args = (array) $args;
	}

	public function __toString() {
		return $this->_fn;
	}

	public function __invoke($callback) {
		$clone = new Predicate($this->_fn);

		foreach($this->_args as $arg) {
			$clone->_args[] = ($arg instanceof Predicate) ? $arg($callback) : $callback($arg);
		}

		return $clone;
	}

	public function getIterator() {
		return new \ArrayIterator($this->_args);
	}

}
