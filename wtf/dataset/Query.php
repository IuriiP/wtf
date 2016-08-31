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

use Field,
	Expression,
	Join,
	Where;

/**
 * Description of Query
 *
 * @author IuriiP <hardwork.mouse@gmail.com>
 */
class Query implements \Wtf\Interfaces\Aggregator {

	use \Wtf\Traits\Aggregator;

	public function __construct($conditions = null) {
		if($conditions) {
			foreach($conditions as $key => $cbranch) {
				foreach($cbranch as $condition) {
					$this->$key = $condition;
				}
			}
		}
	}

	public function getConditions() {
		return $this([
			'join',
			'leftJoin',
			'where',
			'whereOr',
			'having',
			'order',
			'groupBy',
			'limit',
			'offset',
		]);
	}

	public function getReading() {
		return $this(['fields']);
	}

	public function getCreating() {
		return $this(['data']);
	}

	public function getUpdating() {
		return $this(['data']);
	}

}
