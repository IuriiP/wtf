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
 * Common functions
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
		return trim($str, '_');
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
				case 'x': return 'xes';
				case 'y': return 'ies';
				case 'z': return 'zes';
				default: return $matches[0] . 's';
			}
		}, $string);
	}

	/**
	 * Eval string as PHP
	 * 
	 * @param string $string
	 * @return mixed
	 */
	static public function parsePhp($string) {
		$content = null;
		ob_start();
		$content = eval(preg_replace(['~\\<\\?php~', '~\\?\\>~'], '', $string));
		ob_end_clean();
		return (false === $content) ? [] : $content;
	}

	/**
	 * Normalize path: resolves '.' and '..' elements
	 * 
	 * @param string $path
	 * @return string
	 */
	static function normalizePath($path) {
		$path = str_replace(['/', '\\'], '/', $path);
		$parts = array_filter(explode('/', $path), 'strlen');
		$absolutes = [];
		foreach($parts as $part) {
			if('.' == $part)
				continue;
			if('..' == $part) {
				array_pop($absolutes);
			} else {
				$absolutes[] = $part;
			}
		}
		return implode('/', $absolutes);
	}

	/**
	 * Make absolute path, based on root.
	 * 
	 * @param string $path
	 * @param string $root
	 * @return string
	 */
	static function absolutePath($path, $root = null) {
		$path = str_replace('\\', '/', $path);
		$base = str_replace('\\', '/', realpath('/'));
		if('/' === $path{0}) {
			return self::normalizePath($base . '.' . $path);
		}
		if(0 === strpos($path, $base)) {
			return self::normalizePath($path);
		}
		return self::normalizePath(str_replace('\\', '/', realpath($root)) . '/' . $path);
	}

}
