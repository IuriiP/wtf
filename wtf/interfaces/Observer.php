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

namespace Wtf\Interfaces;

/**
 * Observer pattern
 *
 * @author IuriiP <hardwork.mouse@gmail.com>
 */
interface Observer {

	/**
	 * On event firing
	 * 
	 * @param \Wtf\Core\Event $event
	 * @return $this
	 */
	public function notify(\Wtf\Core\Event $event);

	/**
	 * Callback on event enabled.
	 * 
	 * @param \Wtf\Core\Event $event
	 * @return $this
	 */
	public function enable($event);

	/**
	 * Callback on event disabled.
	 * 
	 * @param \Wtf\Core\Event $event
	 * @return $this
	 */
	public function disable($event);
	
	/**
	 * Perform event action.
	 * 
	 * @param \Wtf\Core\Event $event
	 * @param array $subevents
	 * @return $this
	 */
	public function onEvent($event,$subevents=null);
}
