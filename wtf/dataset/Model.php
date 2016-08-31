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
 * Basic Data Model.
 * 
 * Model is 
 *
 * @author IuriiP <hardwork.mouse@gmail.com>
 */
class Model implements \Wtf\Interfaces\Pool, \Wtf\Interfaces\Configurable {

	use \Wtf\Traits\Pool,
	 \Wtf\Traits\Configurable;

	public $id = null;

	private $_model = null;

	private $_dataset = null;

	private $_domain = null;

	private $_primary = null;

	public function __construct($id) {
		$this->id = $id;
		$parts = explode('/', $id);
		$base = array_shift($parts);

		$this->_model = $this->config('model');
		if($parts) {
			$this->_model = array_merge($this->_model, $this->config(...$parts));
		}
	}

	public function dataset() {
		if(!$this->_dataset) {
			$this->_dataset = $this->config('dataset', 'default');
		}
		return $this->_dataset;
	}

	public function domain() {
		if(!$this->_domain) {
			$this->_domain = $this->config('domain', $this->id);
		}
		return $this->_domain;
	}

	public function primary() {
		if(!$this->_primary) {
			$this->_primary = $this->_findPrimary();
		}
		return $this->_primary;
	}

	private function _findPrimary() {
		if($idx = $this->config('index')) {
			return array_search('primary', $idx);
		}

		$idx = array_filter($this->_model, function($data) {
			return $data instanceof Data &&
				$data->definedAs(['SERIAL', 'PRIMARY', 'AUTOINCREMENT']);
		});

		if($idx) {
			return key($idx);
		}
		return null;
	}

	public function resolve($name) {
		if(isset($this->_model[$name])) {
			$data = $this->_model[$name];
			if($data instanceof Data) {
				// direct field
				return "{$this->_domain}:{$name}";
			} elseif($data->isLink()) {
				// linked field
				return $data->set();
			} else {
				// relation
				return $data;
			}
		}
	}

	public function fields($only = []) {
		$ret = array_filter($this->_model, function($data) {
			return $data instanceof Data;
		});
		if($only) {
			return array_intersect_key($ret, array_flip($only));
		}
		return $ret;
	}

	public function links($only = []) {
		$ret = array_filter($this->_model, function($data) {
			return $data instanceof Relation && $data->isLink();
		});
		if($only) {
			return array_intersect_key($ret, array_flip($only));
		}
		return $ret;
	}

	public function relations($only = []) {
		$ret = array_filter($this->_model, function($data) {
			return $data instanceof Relation && !$data->isLink();
		});
		if($only) {
			return array_intersect_key($ret, array_flip($only));
		}
		return $ret;
	}

}
