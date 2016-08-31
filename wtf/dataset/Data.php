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
 * Description of Data
 *
 * @author IuriiP <hardwork.mouse@gmail.com>
 */
class Data implements \Wtf\Interfaces\AsArray {

	use \Wtf\Traits\AsArray;

	const
		PRIMARY = 0,
		SERIAL = 0,
		BOOLEAN = 1,
		INTEGER = 2,
		DOUBLE = 3,
		FLOAT = 3,
		DATETIME = 4,
		STRING = 5,
		TEXT = 6,
		BINARY = 7,
		SPATIAL = 8;

	private static $_keywords = [
		self::PRIMARY => ['SERIAL'],
		self::BOOLEAN => ['BOOL', 'DEFAULT:0'],
		self::INTEGER => ['BIGINT', 'DEFAULT:0'],
		self::DOUBLE => ['DOUBLE', 'DEFAULT:0'],
		self::DATETIME => ['DATETIME', 'NULLABLE'],
		self::STRING => ['STRING:255', 'NULLABLE'],
		self::TEXT => ['TEXT', 'NULLABLE'],
		self::BINARY => ['BLOB', 'NULLABLE'],
		self::SPATIAL => ['GEOMETRY', 'NULLABLE'],
	];

	public static function _() {
		return new Data(...func_get_args());
	}

	public function __construct() {
		$this->_parseAttrs(func_get_args());
	}

	private function _parseAttrs($attrs) {
		foreach((array) $attrs as $value) {
			if(is_string($value)) {
				$parts = explode(':', $value);
				$key = array_shift($parts);
				$this->_array[$key] = $this->_prepareVals($parts);
			} elseif(is_array($value)) {
				$key = array_shift($value);
				$this->_array[$key] = $this->_prepareVals($value);
			} elseif(is_int($value) && isset(self::$_keywords[$value])) {
				$this->_parseAttrs(self::$_keywords[$value]);
			}
		}
	}

	private function _prepareVals($array) {
		return array_map(function($val) {
			if(is_numeric($val)) {
				return intval($val);
			} else {
				return '\'' . trim((string) $val, ' \'"') . '\'';
			}
			return $val;
		}, $array);
	}
	
	public function definedAs($list) {
		return !!array_intersect_key($this->_array, array_flip($list));
	}

	public static function linkTo($model,$links=[]) {
		return Relation::hasOne($model, $links);
	}
	
	public static function hasOne($model,$links=[]) {
		return Relation::hasOne($model, $links);
	}
	public static function hasMany($model,$links=[]) {
		return Relation::hasMany($model, $links);
	}
}
