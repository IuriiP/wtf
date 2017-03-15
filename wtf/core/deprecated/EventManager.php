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
 * Description of EventManager
 *
 * @author IuriiP <hardwork.mouse@gmail.com>
 */
class EventManager implements \Wtf\Interfaces\Singleton {

	use \Wtf\Traits\Singleton;

	/**
	 *
	 * @var \Wtf\Core\EventTree
	 */
	protected $_tree = null;

	/**
	 * Init tree & register configured events.
	 */
	protected function __construct($cfg = null) {
		if(!$this->_tree) {
			$this->_tree = new Tree();
		}
	}

	/**
	 * Implement the array of [$event => [observers], ...]
	 * 
	 * Array of 1:M
	 * 
	 * @param array $list
	 * @return $this
	 */
	public static function observe($list) {
		$self = self::singleton();
		if($list) {
			foreach($list as $event => $observers) {
				$self->register($observers, $event);
			}
		}

		return $self;
	}

	/**
	 * Add the observer to the path.
	 * 
	 * 1:1
	 * 
	 * @param \Wtf\Interfaces\Observer $observer
	 * @param string $event
	 * @return \Wtf\Core\EventManager
	 */
	public static function add($observer, $event = '') {
		return self::singleton()->subscribe($observer, $event);
	}

	/**
	 * Remove the observer(s) from the path.
	 * 
	 * 1:1
	 * 
	 * @param string $event
	 * @param \Wtf\Interfaces\Observer $observer
	 * @return \Wtf\Core\EventManager
	 */
	public static function remove($event, $observer = null) {
		return self::singleton()->unsubscribe($event, $observer);
	}

	/**
	 * Subscribe the observer to events.
	 * 
	 * 1:M
	 * 
	 * @param \Wtf\Interfaces\Observer $observer
	 * @param string[] $events
	 * @return \Wtf\Core\EventManager
	 */
	public static function subscribe(\Wtf\Interfaces\Observer $observer, $events) {
		$self = self::singleton();
		foreach((array) $events as $event) {
			if(!is_string($event)) {
				$event = '';
			}
			$self->_tree->add($observer->enable($event), $event);
		}

		return $self;
	}

	/**
	 * Unsubscribe events from the observer.
	 * 
	 * M:1
	 * 
	 * @param string[] $events
	 * @param \Wtf\Interfaces\Observer $observer
	 * @return \Wtf\Core\EventManager
	 */
	public static function unsubscribe($events, \Wtf\Interfaces\Observer $observer = null) {
		$self = self::singleton();
		foreach((array) $events as $event) {
			if(!is_string($event)) {
				$event = '';
			}

			if($observer) {
				$self->_tree->remove($event, $observer->disable($event));
			} else {
				$self->_tree->each(function($leaf) use($event) {
					$leaf->disable($event);
					return false;
				}, $event);
			}
		}

		return $self;
	}

	/**
	 * Link events to observers.
	 * 
	 * M:M
	 * 
	 * @param \Wtf\Interfaces\Observer[][] $observers
	 * @param string[][] $events
	 * @return \Wtf\Core\EventManager
	 */
	public static function register($observers, $events) {
		$self = self::singleton();
		$olist = (array) $observers;
		$elist = (array) $events;
		reset($elist);

		foreach($olist as $ogroup) {
			$egroup = current($elist);
			foreach((array) $ogroup as $observer) {
				$self->subscribe($observer, $egroup);
			}
			next($elist);
		}

		return $self;
	}

	/**
	 * Unlink events from observers.
	 * 
	 * M:M
	 * 
	 * @param string[][] $events
	 * @param \Wtf\Interfaces\Observer[][] $observers
	 * @return \Wtf\Core\EventManager
	 */
	public static function unregister($events, $observers) {
		$self = self::singleton();
		$olist = (array) $observers;
		$elist = (array) $events;
		reset($elist);

		foreach($olist as $ogroup) {
			$egroup = current($elist);
			foreach((array) $ogroup as $observer) {
				$self->unsubscribe($observer, $egroup);
			}
			next($elist);
		}

		return $self;
	}

	/**
	 * Fire event on current tree.
	 * 
	 * @param \Wtf\Core\Event $event
	 * @return \Wtf\Core\Event
	 */
	public static function fire(\Wtf\Core\Event $event) {
		self::singleton()->_tree->each(function(\Wtf\Interfaces\Observer $leaf) use($event) {
			$leaf->notify($event);
		}, $event->name);

		return $event;
	}

}
