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

namespace Wtf\Dataset\Engines;

/**
 * Description of File
 *
 * @author IuriiP <hardwork.mouse@gmail.com>
 */
class File extends \Wtf\Dataset\Engine {

	private $_format = 'json';

	private $_resource = null;

	private $_readonly = false;

	private $_changed = false;

	private $_content = [];

	public function open() {
		$config = $this->config();
		$this->_resource = \Wtf\Core\Resource::produce(\Wtf\Helper\Complex::get($config, 'resource'));
		$this->_format = \Wtf\Helper\Complex::get($config, 'format', $this->_resource->getType());
		$this->_readonly = \Wtf\Helper\Complex::get($config, 'readonly');
	}

	public function close() {
		if($this->_changed) {
			$this->_store();
		}
		$this->_content = null;
	}

	/**
	 * Check conditions.
	 * 
	 * @param mixed $val
	 * @param array $conditions
	 * @return boolean false if condition is invalid
	 */
	private function _filter($val, $conditions) {
		foreach($conditions as $predicate) {
			if(!$predicate->check($val)) {
				return false;
			}
		}
		return true;
	}

	public function read($conditions) {
		return array_filter($this->_content, function($val) use ($conditions) {
			return $this->_filter($val, $conditions);
		});
	}

	public function create($data) {
		if(!$this->_readonly) {
			array_push($this->_content, (array) $data);
			$this->_changed = true;
			$val = end($this->_content);
			return [key($this->_content) => $val];
		}
		return [];
	}

	public function delete($conditions) {
		$count = count($this->_content);
		if($count && !$this->_readonly) {
			$this->_content = array_filter($this->_content, function($val) use ($conditions) {
				return !$this->_filter($val, $conditions);
			});
		}
		$count = $count - count($this->_content);
		if($count) {
			$this->_changed = true;
		}
		return $count;
	}

	public function update($data, $conditions) {
		$count = 0;
		array_walk($this->_content, function (&$val) use($data, $conditions, &$count) {
			if($this->_filter($val, $conditions)) {
				$val = array_replace($val, $data);
				$count++;
			}
		});
		if($count) {
			$this->_changed = true;
		}
		return $count;
	}

	public function error() {
		
	}

	public function isReady() {
		
	}

	public static function getAttributes(\Wtf\Dataset\Data $data) {
		
	}

//put your code here
}
