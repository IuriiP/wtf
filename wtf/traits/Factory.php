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

namespace Wtf\Traits;

/**
 * Factory for produce a object of the specified class
 * in specified namespace
 *
 * @author Iurii Prudius <hardwork.mouse@gmail.com>
 */
trait Factory {

	/**
	 * Make name in CamelCase style
	 * 
	 * @param string $string
	 * @param boolean $ucfirst
	 * @return string
	 */
	final public static function camelCase($string, $ucfirst = true) {
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
	final public static function snakeCase($string) {
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
	final public static function plural($string) {
		return preg_replace_callback('~[a-z]$~', function($matches) {
			switch($matches[0]) {
				case 's': return 'ses';
				case 'y': return 'ies';
				default: return $matches[0] . 's';
			}
		}, $string);
	}

	/**
	 * Object Factory
	 * 
	 * @param mixed $named string: full class name, array: [namespace,name], object: prototype
	 * @param array $args args list
	 * @return mixed|null
	 */
	final public static function factory($named, $args = []) {
		if(is_array($named)) {
			$ns = $named[0] ? \Wtf\Core\App::config(self::plural($named[0]), $named[1]) : '';
			$class = ($ns? : self::plural(get_called_class())) . '\\' . self::camelCase($named[1]);
		} elseif(is_string($named)) {
			$class = \Wtf\Core\App::get($named) or $named;
		} elseif(is_object($named)) {
			$class = get_class($named);
		} else {
			return null;
		}
		if(is_object($class)) {
			// it's contract for singleton
			return $class;
		}
		try {
			$ref = new \ReflectionClass($class);
			return $ref->newInstanceArgs($args);
		} catch(Exception $exc) {
			trigger_error(__CLASS__ . "::Factory: error istantiating '{$class}'");
		}
		return null;
	}

	/**
	 * Direct call to factory
	 * 
	 * @return mixed|null
	 */
	final public static function make() {
		$args = func_get_args();
		$named = array_shift($args);
		return self::factory($named, $args);
	}

	/**
	 * Smart call to factory
	 * 
	 * @param string $name
	 * @param array $params
	 * @return mixed|null
	 */
	final static function __callStatic($name, $params) {
		return static::factory(['', $name], $params);
	}

}
