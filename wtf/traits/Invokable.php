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
 * Implementation of Invokable
 *
 * @author IuriiP <hardwork.mouse@gmail.com>
 */
trait Invokable {

	public function __invoke() {
		$args = func_get_args();

		if($args) {
			$target = array_shift($args);

			if($this instanceof \Wtf\Interfaces\Caller) {
				return $this->$target(...$args);
			} elseif($this instanceof \ArrayAccess) {
				$obj = $this[$target];
			} elseif($this instanceof \Wtf\Interfaces\Getter) {
				$obj = $this->$target;
			}

			if($obj) {
				return $obj(...$args);
			}

			throw new \ErrorException('Unknown member ' . __CLASS__ . "::{$target} on invoke");
		}

		return $this;
	}

}
