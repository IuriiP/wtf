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

namespace Wtf\Core;

/**
 * DataMapper is interface to database.
 * 
 * Example:
 * 
 * $user = DataMapper::_('user')->first(['login'=>App::request('login'),'md5pass'=>md5(App::request('password'))]);
 * if(1===$user->count()) {
 * 
 * }
 *
 * @author IuriiP <hardwork.mouse@gmail.com>
 */
class DataMapper {

	protected $model = null;

	protected $dataset = null;

	protected $domain = null;

	public static function _() {
		return new static(...func_get_args());
	}

	public function __construct($model) {
		$this->model = \Wtf\Dataset\Model::instance($model);
		$this->dataset = Dataset::instance($this->model->dataset());
		$this->domain = $this->model->domain();
	}

	public function count($conditions) {
		return $this->dataset->count(\Wtf\Dataset\Query::_($conditions)
					->domain($this->domain)
					->offset(0)
					->limit(0)
					->getConditions());
	}

	/**
	 * Read all data.
	 * 
	 * @param \Wtf\Dataset\Condition $conditions
	 * @return \Wtf\Dataset\Result
	 */
	public function all($conditions, $fields = []) {
		if($fields) {
			// alias => fieldname
		} else {
			
		}
		$flist = $this->model->fields($fields);
		$llist = $this->model->links($fields);
		$qb = \Wtf\Dataset\Query::_($conditions)
			->domain($this->domain)
			->fields($flist)
			->offset(0)
			->limit(0);
		return $this->dataset->read($qb->getReading(), $qb->getConditions());
	}

	/**
	 * Read first datum.
	 * 
	 * @param \Wtf\Dataset\Condition $conditions
	 * @return \Wtf\Dataset\Result
	 */
	public function first($conditions) {
		$qb = \Wtf\Dataset\Query::_($conditions)
			->domain($this->domain)
			->fields($this->reading())
			->offset(0)
			->limit(1);
		return $this->_source->read($qb->getReading(), $qb->getConditions());
	}

	/**
	 * Read the page of data.
	 * 
	 * @param \Wtf\Dataset\Condition $conditions
	 * @param int $pageno
	 * @param int $pagesize
	 * @return \Wtf\Dataset\Result
	 */
	public function page($conditions, $pageno, $pagesize = 0) {
		$this->_pagesize = $pagesize? : $this->_pagesize;
		$qb = \Wtf\Dataset\Query::_($conditions)
			->domain($this->domain)
			->fields($this->reading())
			->offset($pageno * $this->_pagesize)
			->limit($this->_pagesize);
		return $this->_source->read($qb->getReading(), $qb->getConditions());
	}

	/**
	 * Append the data.
	 * 
	 * @param mixed $data
	 * @return \Wtf\Dataset\Result
	 */
	public function append($data) {
		return $this->_source->create(\Wtf\Dataset\Query::_($conditions)
					->domain($this->domain)
					->data($this->creating($data))
					->getCreating());
	}

	/**
	 * Update the data.
	 * 
	 * @param mixed $data
	 * @param \Wtf\Dataset\Condition $conditions
	 * @return \Wtf\Dataset\Result
	 */
	public function put($data, $conditions) {
		$qb = \Wtf\Dataset\Query::_($conditions)
			->domain($this->domain)
			->data($this->updating($data));
		return $this->_source->update($qb->getUpdating(), $qb->getConditions());
	}

	/**
	 * Remove by conditions.
	 * 
	 * @param \Wtf\Dataset\Condition $conditions
	 * @return \Wtf\Dataset\Result
	 */
	public function remove($conditions) {
		return $this->_source->delete(\Wtf\Dataset\Query::_($conditions)
					->domain($this->domain)
					->getConditions());
	}

	/**
	 * Helper to get reading list.
	 * 
	 * @return array of alias => field
	 */
	protected function reading() {
		return $this->map['reading'];
	}

	/**
	 * Helper to get styled writing list.
	 * 
	 * @return array
	 */
	protected function creating($data) {
		$map = $this->map['creating'] ? : $this->map['writing'];
		if($map) {
			$out = [];
			foreach($data as $key => $value) {
				if(isset($map[$key])) {
					$out[$map[$key]] = $value;
				}
			}
			return $out;
		}
		return $data;
	}

	protected function updating($data) {
		$map = $this->map['updating'] ? : $this->map['writing'];
		if($map) {
			$out = [];
			foreach($data as $key => $value) {
				if(isset($map[$key])) {
					$out[$map[$key]] = $value;
				}
			}
			return $out;
		}
		return $data;
	}

}
