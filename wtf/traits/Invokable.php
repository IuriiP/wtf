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
		$arguments = func_get_args();

		$obj = $this;
		while($arguments) {
			$target = array_shift($arguments);
			if(is_array($obj)) {
				if(isset($obj[$target])) {
					$obj = $obj[$target];
					continue;
				}
			} elseif(is_object($obj)) {
				if(isset($obj->$target)) {
					$obj = $obj->$target;
					continue;
				} elseif($obj instanceof \ArrayAccess && isset($obj[$target])) {
					$obj = $obj[$target];
					continue;
				} elseif(is_callable([$obj,$target])) {
					return call_user_func_array([$obj,$target],$arguments);
				} elseif(($obj !== $this) && is_callable($obj)) {
					// prevent deadloop
					$obj = $obj($target, ...$arguments);
					continue;
				}
			}
			throw new \Wtf\Exceptions\MethodException(__CLASS__ . "::{$target}");
		}

		return $obj;
	}

}
