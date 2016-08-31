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
 * Description of Logger
 *
 * @author IuriiP <hardwork.mouse@gmail.com>
 */
class Logger extends Observer implements \Wtf\Interfaces\Singleton, \Wtf\Interfaces\Configurable {

	use \Wtf\Traits\Singleton,
	 \Wtf\Traits\Configurable;

	private $_target = null;

	protected function __construct() {
		if($this->config('enabled')) {
			$this->_target = $config['target'] ? : null;
		}
	}

	public function onEvent($event, $subevents = null) {
		if(in_array($event->type, $this->config('levels')? : [$event->type])) {
			$target = $this->_target;
			if($target) {
				// log on resource
				if($target instanceof \Wtf\Interfaces\Writable) {
					$target->append([
						'level' => $event->type,
						'event' => $event->name,
						'time' => $event->time,
						'message' => $event->message,
						'source' => $event->source,
						'data' => $event->data,
					]);
				}
			} else {
				// log into trashbin
				printf(__CLASS__ . "::%s:%s\t%d\t'%s'\t[%s]\t[%s]", $event->name, date('c', $event->time), $event->type, implode(' & ',$event->message), implode(', ',\Wtf\Helper\Html::showAttrs($event->source)), implode(', ',\Wtf\Helper\Html::showAttrs($event->data)));
			}
		}
	}

}
