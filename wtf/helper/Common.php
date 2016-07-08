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

namespace Wtf\Helper;

/**
 * Description of Common
 *
 * @author IuriiP <hardwork.mouse@gmail.com>
 */
abstract class Common {

	/**
	 * Make name in CamelCase style
	 * 
	 * @param string $string
	 * @param boolean $ucfirst
	 * @return string
	 */
	static public function camelCase($string, $ucfirst = true) {
		$str = preg_replace_callback('~_([a-z])~i', function($matches) {
			return ucfirst($matches[1]);
		}, $string);
		return $ucfirst ? ucfirst($str) : $str;
	}

	/**
	 * Make name in snake_case style
	 * 
	 * @param string $string
	 * @return string
	 */
	static public function snakeCase($string) {
		$str = preg_replace_callback('~[A-Z]~', function($matches) {
			return '_' . strtolower($matches[0]);
		}, lcfirst($string));
		return $str;
	}

	/**
	 * Pluralise last element in string
	 * 
	 * @param string $string
	 * @return string
	 */
	static public function plural($string) {
		return preg_replace_callback('~[a-z]$~', function($matches) {
			switch($matches[0]) {
				case 's': return 'ses';
				case 'y': return 'ies';
				default: return $matches[0] . 's';
			}
		}, $string);
	}

	static public function parsePhp($string) {
		ob_start();
		$content = eval(preg_replace(['~\\<\\?php~', '~\\?\\>~'], '', $string));
		ob_end_clean();
		return $content;
	}

}
