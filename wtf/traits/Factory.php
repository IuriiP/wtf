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

use Wtf\Helper\Common;

/**
 * Factory for produce a object of the specified class
 * in specified namespace
 *
 * @author Iurii Prudius <hardwork.mouse@gmail.com>
 */
trait Factory {

	/**
	 * Object Factory
	 * 
	 * @param mixed $named string: full class name, array: [namespace,name], object: prototype
	 * @param array $args args list
	 * @return mixed|null
	 */
	final public static function factory($named, $args = []) {
		if(is_array($named)) {
			$ns = '';
			if($named[0] && ($context = \Wtf\Core\Config::singleton()->get(Common::plural($named[0])))) {
				$ns = $context($named[1])? : '';
			}
			$class = ($ns? : Common::plural(get_called_class())) . '\\' . Common::camelCase($named[1]);
		} elseif(is_object($named)) {
			$class = get_class($named);
		} elseif(is_string($named)) {
			$class = \Wtf\Core\App::singleton()->get($named)? : $named;
		} else {
			return null;
		}
		if(is_object($class)) {
			// it's contract for object
			return $class;
		}

		$ref = new \ReflectionClass($class);
		return $ref->newInstanceArgs($args);
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
		return self::factory(['', $name], $params);
	}

}
