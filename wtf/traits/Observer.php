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

namespace Wtf\Traits;

/**
 * Description of Observer
 *
 * @author IuriiP <hardwork.mouse@gmail.com>
 */
trait Observer {

	/**
	 * On event firing
	 * 
	 * @param \Wtf\Core\Event $event
	 * @return $this
	 */
	public function notify(\Wtf\Core\Event $event) {
		$path = explode('/', "onEvent/{$event->name}");
		$args = [];

		while($path) {
			$method = implode('_', $path);
			if(method_exists($this, $method)) {
				call_user_func([$this, $method], $event, $args);
				return $this;
			}
			array_unshift($args, array_pop($path));
		}

		return $this;
	}

	/**
	 * Mark event as enabled. Generic mock.
	 * 
	 * @param string $event
	 * @return $this
	 */
	public function enable($event) {
		return $this;
	}

	/**
	 * Mark event as disabled. Generic mock.
	 * 
	 * @param string $event
	 * @return $this
	 */
	public function disable($event) {
		return $this;
	}

}
