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

/**
 *
 * @author IuriiP <hardwork.mouse@gmail.com>
 */
interface Tree {

	/**
	 * Is current tree empty.
	 */
	public function isEmpty();

	/**
	 * Add the leaf to the path.
	 * 
	 * @param mixed $object
	 * @param string|array $path
	 */
	public function add($object, $path = '');

	/**
	 * Remove the object from the path.
	 * 
	 * @param string|array $path
	 * @param mixed $object
	 */
	public function remove($path, $object = null);

	/**
	 * Get leaves on the path.
	 * 
	 * @param string|array $path
	 * @return Wtf\Core\Collection
	 */
	public function leaves($path);

	/**
	 * Get branches on the path.
	 * 
	 * @param string|array $path
	 * @return Wtf\Core\Collection
	 */
	public function branches($path);

	/**
	 * Perform the callback on each leaf on the path.
	 * 
	 * @param Callable $callback
	 * @param string|array $path
	 * @return $this
	 */
	public function each($callback, $path = '');
}
