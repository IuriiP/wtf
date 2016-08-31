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
 * Description of Relation
 *
 * @author IuriiP <hardwork.mouse@gmail.com>
 */
class Relation {

	/**
	 * Is direct link.
	 * 
	 * @var bool
	 */
	private $_one = null;

	private $_target = null;
	
	private $_set = null;

	private $_links = null;

	/**
	 * @param bool $one
	 * @param string $name
	 * @param array|string|null $links
	 */
	public function __construct($one, $name, $links) {
		$this->_one = $one;
		$list = preg_split('~[\.:\\\/]~', $name,null,PREG_SPLIT_NO_EMPTY);
		$domain = array_shift($list);
		$this->_target = Model::instance($domain);
		$this->_set = $list;
		$this->_links = is_array($links) ? $links : [ $domain => ($links? : $this->_target->getPrimary())];
	}

	/**
	 * Relation 1:1
	 * 
	 * @param string $name
	 * @param array|string|null $links
	 * @return \Wtf\Dataset\Relation
	 */
	public static function hasOne($name, $links = []) {
		return new Relation(true, $name, $links);
	}
	
	/**
	 * Relation 1:M
	 * 
	 * @param string $name
	 * @param array|string|null $links
	 * @return \Wtf\Dataset\Relation
	 */
	public static function hasMany($name, $links = []) {
		return new Relation(false, $name, $links);
	}

	public function isLink() {
		return $this->_one;
	}
	
	public function target() {
		return $this->_target->id;
	}
	
	public function links() {
		return $links;
	}

}
