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
 * Description of EventTree
 *
 * @author IuriiP <hardwork.mouse@gmail.com>
 */
class EventTree {

	/**
	 *
	 * @var array of \Wtf\Interfaces\Observer
	 */
	private $_observers = [];

	/**
	 *
	 * @var \Wtf\Core\EventCollection
	 */
	private $_children = null;

	/**
	 * Enable events listening
	 * 
	 * @param array|string $events
	 * @param array|\Wtf\Interfaces\Observer $observers
	 */
	public function enable($events, $observers = null) {
		$elist = (array) $events;

		if($observers) {
			// array of events
			$olist = (array) $observers;
			array_map(function($event,$observer) {
				if(is_array($event)) {
					
				}
				$this->register($observer, explode('/', $event));
			}, $elist, $olist);
		}
		return $this;
			$observer->notify(Event::builder('self/enabled')->data(['name'=>$name]));
	}

	/**
	 * Disable events listening
	 * 
	 * @param array|string $events
	 * @param string|array $aliases
	 */
	public function disable($events, $aliases = []) {
		
		return $this;
	}

	/**
	 * 
	 * @param \Wtf\Core\Event $event
	 */
	public function notify(\Wtf\Core\Event $event) {
		$this->fire($event, explode('/', $event->name));
		return $this;
	}

	/**
	 * Register a lot of observers for a lot of events.
	 * 
	 * @param string[] $events
	 * @param \Wtf\Interfaces\Observer[] $observers
	 */
	protected function multiRegister($events,$observers) {
		$elist = (array) $events;
		$olist = (array) $observers;
		foreach($elist as $event) {
			$path = explode('/', $event);
			foreach($olist as $observer) {
				$this->register($observer, $path);
			}
		}
	}
	
	/**
	 * Internal implementation of $this->enable()
	 * 
	 * @param \Wtf\Interfaces\Observer $observer
	 * @param array $path
	 */
	protected function register(\Wtf\Interfaces\Observer $observer, $path) {
		$seek = array_shift($path);
		if(!$seek) {
			$this->_observers[$observer->alias()] = $observer;
		} else {
			if(!$this->_children) {
				$this->_children = new EventCollection();
			}
			$tree = new EventTree();
			$tree->register($observer, $path);
			$this->_children[$seek] = $tree;
		}
		return $this;
	}

	/**
	 * Internal implementation of $this->disable()
	 * 
	 * @param string $alias
	 * @param array $path
	 */
	protected function unregister($alias, $path) {
		if(!$seek) {
			if(isset($this->_observers[$alias])) {
				$this->_observers[$alias]->notify(new Event('self/disabled'));
			}
		} else {
			unset($this->_children[$seek]);
		}
		return $this;
	}

	protected function fire($event, $path) {
		if($this->_observers) {
			foreach($this->_observers as $observer) {
				$observer->notify($event);
			}
		}
		if(($step = array_shift($path)) && ($child = $this->_children[$step])) {
			$child->fire($event, $path);
		}
	}

}
