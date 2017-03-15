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
 * Implementation of Wtf\Interfaces\Contractor
 *
 * @author IuriiP <hardwork.mouse@gmail.com>
 */
trait Contractor {

	use Caller;
	
	protected static $_contracts = [];

	/**
	 * Set/get the named contract.
	 * 
	 * @param string $name
	 * @param mixed $instance
	 * @return mixed
	 */
	public static function contract($name, $instance = null) {
		$name = strtolower($name);
		if($instance) {
			self::$_contracts[$name] = $instance;
		}

		return isset(self::$_contracts[$name]) ? self::$_contracts[$name] : null;
	}

	/**
	 * Get contract.
	 * 
	 * @param string $name
	 */
	public function __get($name) {
		return self::contract($name);
	}

	/**
	 * Check if contract exists.
	 * 
	 * @param string $name
	 */
	public function __isset($name) {
		return !!self::contract($name);
	}

	/**
	 * Disable setting contract.
	 * 
	 * @param string $name
	 * @param mixed $value
	 */
	public function __set($name, $value) {
//		self::contract($name, $value);
		throw new \Wtf\Exceptions\ReadOnlyException(__CLASS__);
	}

	/**
	 * Disable removing contract.
	 * 
	 * @param type $name
	 */
	public function __unset($name) {
//		unset(self::$_contracts[strtolower($name)]);
		throw new \Wtf\Exceptions\ReadOnlyException(__CLASS__);
	}

}
