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

namespace Wtf\Core;

/**
 * Session expands access to $_SESSION.
 *
 * @author Iurii Prudius <hardwork.mouse@gmail.com>
 */
class Session implements \Wtf\Interfaces\Singleton, \Wtf\Interfaces\Invokable, \ArrayAccess {

	use \Wtf\Traits\Singleton;

	/**
	 * Session starts if inactive.
	 */
	private function __construct() {
		if(\PHP_SESSION_ACTIVE !== session_status()) {
			session_start();
		}
	}

	/**
	 * Write & close session.
	 */
	public function __destruct() {
		session_commit();
	}

	/**
	 * Check if record exists.
	 * 
	 * @param string $offset
	 * @return bool
	 */
	public function offsetExists($offset) {
		return isset($_SESSION[strtolower($offset)]);
	}

	/**
	 * Get current value.
	 * Unset if it is temporary.
	 * 
	 * @param string $offset
	 * @return mixed
	 */
	public function offsetGet($offset) {
		$name = strtolower($offset);

		if(isset($_SESSION[$name])) {
			// decode session value
			$record = $_SESSION[$name];
			if(is_scalar($record)) {
				return $record;
			}

			if(isset($record['once'])) {
				unset($_SESSION[$name]);
			}

			return unserialize($record['value']);
		}

		return null;
	}

	/**
	 * Set permanent value.
	 * 
	 * @param string $offset
	 * @param mixed $value
	 */
	public function offsetSet($offset, $value) {
		$_SESSION[strtolower($offset)] = is_scalar($value) ? $value : ['value' => serialize($value)];
	}

	/**
	 * Unset record.
	 * 
	 * @param string $offset
	 */
	public function offsetUnset($offset) {
		unset($_SESSION[strtolower($offset)]);
	}

	/**
	 * Set temporary value.
	 * 
	 * @param string $offset
	 * @param mixed $value
	 */
	public function flush($offset, $value) {
		$_SESSION[strtolower($offset)] = ['once' => true, 'value' => serialize($value)];
	}

	public function __invoke($offset) {
		return $this->offsetGet($offset);
	}

}
