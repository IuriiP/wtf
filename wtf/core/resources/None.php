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

namespace Wtf\Core\Resources;

/**
 * None is fake unstructured resource.
 * It is equal to dev/null
 *
 * @author Iurii Prudius <hardwork.mouse@gmail.com>
 */
class None extends \Wtf\Core\Resource implements \Wtf\Interfaces\Writable {

	public function __construct() {
	}

	/**
	 * Check if already exists.
	 * 
	 * @return bool
	 */
	public function exists() {
		return false;
	}

	/**
	 * @return FALSE
	 */
	public function isContainer() {
		return FALSE;
	}

	/**
	 * @return null
	 */
	public function getScheme() {
		return 'none';
	}

	/**
	 * @return null
	 */
	public function child($name) {
		return null;
	}

	/**
	 * @return null
	 */
	public function container() {
		return null;
	}

	/**
	 * @return int
	 */
	public function getTime($type = NULL) {
		return time();
	}

	/**
	 * @return string
	 */
	public function getPath() {
		return '';
	}

	/**
	 * @return string
	 */
	public function getName() {
		return '';
	}

	/**
	 * @return string
	 */
	public function getType() {
		return '';
	}

	/**
	 * @return string
	 */
	public function getMime() {
		return '';
	}

	/**
	 * @return int
	 */
	public function getLength() {
		return 0;
	}

	/**
	 * @return []
	 */
	public function get($keep = false) {
		return [];
	}

	/**
	 * @return null
	 */
	public function getContent() {
		return null;
	}

	/**
	 * @return null
	 */
	public function append($data) {
		return null;
	}

	/**
	 * @return null
	 */
	public function put($data) {
		return null;
	}

	/**
	 * @return null
	 */
	public function remove() {
		return null;
	}

}
