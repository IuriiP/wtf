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

namespace Wtf\Core;

/**
 * Event with builder.
 * 
 * Using:
 * 
 * $event = new Event('some/warning');
 * $event->fire('message', Event::WARNING, ['data'=>'some data']);
 * 
 * or
 * 
 * Event::_('some/warning')
 *			->message('message')
 *			->type(Event::WARNING)
 *			->data(['data'=>'some data'])
 *			->fire();
 *
 * @author IuriiP <hardwork.mouse@gmail.com>
 */
class Event implements \Wtf\Interfaces\Builder {

	use \Wtf\Traits\Builder;

	/**
	 * Constants
	 */
	const NOTIFY = 0,
		DEBUG = 1,
		INFO = 2,
		MESSAGE = 3,
		WARNING = 4,
		ERROR = 5,
		FAIL = 6;

	/**
	 * Event name
	 * @var string
	 */
	public $name = null;

	/**
	 * Event source
	 * @var mixed
	 */
	public $source = null;

	/**
	 * Event firing time
	 * @var double 
	 */
	public $time = 0;

	/**
	 * Construct named event.
	 * 
	 * @param string $name
	 * @param mixed $source
	 */
	public function __construct($name, $source = null) {
		$this->name = $name;
		$this->source = $source ? : debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2)[1];
	}

	/**
	 * Build instance of Event.
	 * 
	 * @param type $name
	 * @return \Wtf\Core\Event
	 */
	public static function _() {
		return new Event(func_get_arg(0), debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2)[1]);
	}

	/**
	 * Fire event
	 * 
	 * @param string $message
	 * @param int $type
	 * @param mixed $data
	 */
	public function fire($message = '', $type = 0, $data = null) {
		$this->time = microtime(true);
		$this->_bricks['message'] = $message ? [(string) $message] : ($this->message? : ['']);
		$this->_bricks['type'] = $type ? [(int) $type] : ($this->type? : [0]);
		$this->_bricks['data'] = $data ? (array) $data : ($this->data? : []);
		
		return EventManager::fire($this);
	}

}
