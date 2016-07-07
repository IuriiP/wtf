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
 * Singleton functionality for Wtf\Interfaces\Singleton
 *
 * @author Iurii Prudius <hardwork.mouse@gmail.com>
 */
trait Singleton {

	protected static $instance = null;

	/**
	 * Singleton get instance
	 * 
	 * @return \static
	 */
	public static function singleton() {
		if(!self::$instance) {
			$args = func_get_args();
			self::$instance = new static(...$args);
		}
		return self::$instance;
	}

	/**
	 * Construct protect
	 */
	private function __construct() {
		
	}

	/**
	 * Cloning protect
	 * 
	 * @throws \ErrorException
	 */
	private function __clone() {
		throw new \ErrorException(__CLASS__ . '::Singleton: cloning is not allowed',E_ERROR);
	}

	/**
	 * Unserializing protect
	 * 
	 * @throws \ErrorException
	 */
	private function __wakeup() {
		throw new \ErrorException(__CLASS__ . '::Singleton: unserializing is not allowed',E_ERROR);
	}

}
