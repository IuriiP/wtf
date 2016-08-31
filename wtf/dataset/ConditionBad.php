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
 * Description of Condition
 *
 * @author IuriiP <hardwork.mouse@gmail.com>
 */
class Condition implements \IteratorAggregate {

	static private $_opermap = [
		'' => 'eq',
		'=' => 'eq',
		'==' => 'eq',
		'is' => 'eq',
		'IS' => 'eq',
		'<>' => 'ne',
		'><' => 'ne',
		'!=' => 'ne',
		'>' => 'gt',
		'<' => 'lt',
		'>=' => 'ge',
		'=>' => 'ge',
		'<=' => 'le',
		'=<' => 'le',
		'in' => 'in',
		'IN' => 'in',
		'between' => 'bw',
		'BETWEEN' => 'bw',
	];

	private $_glue = null;

	private $_list = [];

	private $_special = [];

	public function __construct($start = null) {
		if(is_array($start)) {
			reset($start);
			$this->_glue = is_string(key($start)) ? key($start) : 'and';
			foreach($start as $value) {
				if(is_scalar($value)) {
					$this($value);
				} else {
					$this(...$value);
				}
			}
		} else {
			$this->_glue = $start ? (string) $start : 'and';
		}
	}

	public function __invoke($first, $second = null, $third = null) {
		switch(func_num_args()) {
			case 1:
				if(is_array($first)) {
					$this->_list[] = new Condition($first);
				} elseif($first instanceof Condition) {
					$this->_list[] = $first;
				} else {
					$this->_list[] = [ $first, 'eq', true];
				}
				break;
			case 2: // equals contracted
				$this->_list[] = [ $first, 'eq', $second];
				break;
			default: // direct condition
				if(is_null($second)) {
					$this->_list[] = [ $first, 'eq', $third];
				} else {
					$this->_list[] = [ $first, isset(self::$_opermap[$second]) ? self::$_opermap[$second] : strtolower($second), $third];
				}
		}
		return $this;
	}

	public function __toString() {
		return $this->_glue;
	}

	public function __call($name, $params) {
		if(!isset($this->_special[$name])) {
			$this->_special[$name] = [];
		}
		if($params) {
			$this->_special[$name] = array_merge($this->_special[$name], $params);
		} else {
			unset($this->_special[$name]);
		}

		return $this;
	}

	public function __get($name) {
		return isset($this->_special[$name]) ? $this->_special[$name] : null;
	}

	public function getIterator() {
		return new \ArrayIterator($this->_list);
	}

}
