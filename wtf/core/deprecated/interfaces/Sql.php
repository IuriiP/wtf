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

namespace Wtf\Interfaces;

use \Wtf\Dataset\Query;

/**
 *
 * @author IuriiP <hardwork.mouse@gmail.com>
 */
interface Sql {

	/**
	 * Direct selection
	 * 
	 * @param \Wtf\Dataset\Query $query
	 * @return \Wtf\Dataset\Result Set
	 */
	public function sqlSelect(Query $query);

	/**
	 * Direct insert
	 * 
	 * @param \Wtf\Dataset\Query $query
	 * @return \Wtf\Dataset\Result Count
	 */
	public function sqlInsert(Query $query);

	/**
	 * Direct update
	 * 
	 * @param \Wtf\Dataset\Query $query
	 * @return \Wtf\Dataset\Result Count
	 */
	public function sqlUpdate(Query $query);

	/**
	 * Direct deletion
	 * 
	 * @param \Wtf\Dataset\Query $query
	 * @return \Wtf\Dataset\Result Count
	 */
	public function sqlDelete(Query $query);

	/**
	 * Count by conditions
	 * 
	 * @param \Wtf\Dataset\Query $query
	 * @return int
	 */
	public function count(Query $query);

	//
	public function joins(Query $query);

	public function wheres(Query $query);

	public function havings(Query $query);

	public function limits(Query $query);

	public function fields(Query $query);
}
