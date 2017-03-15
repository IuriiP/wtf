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

use Wtf\Core\Response;

/**
 * Request incapsulate request information.
 *
 * @author Iurii Prudius <hardwork.mouse@gmail.com>
 */
class Request {

	/**
	 * Request method
	 * @var string
	 */
	private $_method = null;

	/**
	 * Output format
	 * @var string
	 */
	private $_format = null;

	/**
	 * Input data
	 * @var \Wtf\Core\Input
	 */
	private $_input = null;

	private $_accept = [];

	/**
	 * Set method
	 * 
	 * @param string $method
	 * @return \Wtf\Core\Request
	 */
	public function method($method) {
		$this->_method = strtolower($method);
		return $this;
	}

	/**
	 * Set format
	 * 
	 * @param string $format
	 * @return \Wtf\Core\Request
	 */
	public function format($format) {
		$this->_format = $format;
		return $this;
	}

	/**
	 * Set input
	 * 
	 * @param \Wtf\Core\Input $input
	 * @return \Wtf\Core\Request
	 */
	public function input($input) {
		$this->_input = $input;
		return $this;
	}

	/**
	 * Magic setting for acceptable
	 * 
	 * @param string $name
	 * @param string[] $arguments
	 * @return \Wtf\Core\Request
	 */
	public function __call($name, $arguments) {
		$this->_accept[$name] = $this->_priority($list);
		return $this;
	}

	/**
	 * Magic getter for readonly
	 * 
	 * @param string $name
	 * @return string
	 */
	public function __get($name) {
		switch($name) {
			case 'method':
				return $this->_method;
			case 'format':
				return $this->_format;
			case 'input':
				return $this->_input;
			default:
				return reset($this->_accept[$name]);
		}
		
	}

	/**
	 * Select preferable option from acceptable
	 * 
	 * @param string $name
	 * @param string[] $options
	 * @return string
	 */
	public function select($name,$options) {
		if(isset($this->_accept[$name])) {
			$list = $this->_accept[$name];
			foreach($list as $key=>$val) {
				$pattern = str_replace('*', '.*', $key);
				$matches = preg_grep($pattern, $options);
				if($matches) {
					return reset($matches);
				}
			}
		}
		return null;
	}

	/**
	 * Parse list of values, weighted with the quality value syntax
	 * 
	 * @param array|string $list
	 * @return array
	 */
	private function _priority($list) {
		$ret = [];
		foreach((array) $list as $string) {
			if(preg_match_all('~(.+)(?:;q=([\\d.]+))?(?:,|$)~', $string, $matches, PREG_SET_ORDER)) {
				foreach($matches as $record) {
					$ret[$record[1]] = $record[2] ? : '1.0';
				}
			}
		}
		asort($ret);

		return $ret;
	}

}
