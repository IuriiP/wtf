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

	private static $_contracts = [];

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
			static::$_contracts[$name] = $instance;
		}

		return isset(static::$_contracts[$name]) ? static::$_contracts[$name] : null;
	}

	/**
	 * Invoke contract.
	 * 
	 * @param string $name
	 * @param array $arguments
	 * @return mixed
	 * @throws ErrorException on unknown contract
	 */
	public function __call($name, $arguments) {
		$contract = static::contract($name);

		if($contract) {
			return $contract(...$arguments);
		}

		throw new ErrorException("Unknown contract '{$name}'");
	}

	/**
	 * Get contract.
	 * 
	 * @param string $name
	 */
	public function __get($name) {
		return static::contract($name);
	}

	/**
	 * Check if contract exists.
	 * 
	 * @param string $name
	 */
	public function __isset($name) {
		return !!static::contract($name);
	}

	/**
	 * Set contract.
	 * 
	 * @param string $name
	 * @param mixed $value
	 */
	public function __set($name, $value) {
		static::contract($name, $value);
	}

	/**
	 * Remove contract.
	 * 
	 * @param type $name
	 */
	public function __unset($name) {
		unset(static::$_contracts[strtolower($name)]);
	}

}
