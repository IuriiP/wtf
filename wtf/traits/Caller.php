<?php

/*
 * Copyright (C) 2017 IuriiP <hardwork.mouse@gmail.com>
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
 * Implementation of \Wtf\Interfaces\Caller
 * Indirect invoke of member.
 * @uses \Wtf\Interfaces\Singleton
 * 
 * @author IuriiP <hardwork.mouse@gmail.com>
 */
trait Caller {

	public function __call($name, $arguments) {
		if(isset($this->$name)) {
			$obj = $this->$name;
		} elseif($this instanceof \ArrayAccess && isset($this[$name])) {
			$obj = $this[$name];
		} else {
			throw new \Wtf\Exceptions\MethodException(__CLASS__."::{$name}");
		}

		if($arguments) {
			if(is_object($obj)) {
				if(is_callable($obj)) {
					return $obj(...$arguments);
				} else {
					$method = array_shift($arguments);
					return call_user_func_array([$obj, $method], $arguments);
				}
			}
			throw new \Wtf\Exceptions\ArgumentsException(__CLASS__."::{$name}");
		}
		return $obj;
	}

	public static function __callStatic($name, $arguments) {
		$ref = new \ReflectionClass(static::class);
		if($ref->implementsInterface(\Wtf\Interfaces\Singleton::class)) {
			return static::singleton()->__call($name, $arguments);
		}
		throw new \Wtf\Exceptions\SingletonException(__CLASS__);
	}

}
