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
 * Base for the Event
 *
 * @author IuriiP <hardwork.mouse@gmail.com>
 */
class Event {

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
     * Registered subscribers for each named event.
     * @var array of \Wtf\Interface\Observer[] 
     */
    static private $_subscribers = [];

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
     * Event type
     * @var int 
     */
    public $type = 0;

    /**
     * Event message
     * @var string
     */
    public $message = null;

    /**
     * Event specific data
     * @var mixed 
     */
    public $data = null;

    /**
     * Create event
     * 
     * @param string $name
     * @param mixed $source
     */
    public function __construct($name, $source = null) {
        $this->name = $name;
        $this->source = $source;
    }

    /**
     * Fire event with data
     * 
     * @param string $message
     * @param int $type
     * @param mixed $data
     */
    public function fire($message, $type = 0, $data = null) {
        $this->time = microtime(true);
        $this->message = $message;
        $this->type = $type;
        $this->data = $data;
        foreach (self::$_subscribers as $pattern => $observers) {
            if (preg_match($pattern, $name)) {
                foreach ($observers as $regname => $observer) {
                    $observer->notify($this, $regname);
                }
            }
        }
    }

    /**
     * Add observer for event
     * 
     * @param \Wtf\Interfaces\Observer $observer
     * @param string $pattern
     */
    static public function enable(\Wtf\Interfaces\Observer $observer, $pattern = '.*') {
        $pattern = '/' . str_replace('/', '', $pattern) . '/';
        $named = $observer->observer($name);
        if (!isset(self::$_subscribers[$pattern]) || !isset(self::$_subscribers[$pattern][$named])) {
            self::$_subscribers[$pattern][$named] = $observer;
        }
    }

    /**
     * Remove observer from event
     * 
     * @param type $pattern
     * @param type $observername
     */
    static public function disable($pattern, $observername = null) {
        $pattern = '/' . str_replace('/', '', $pattern) . '/';
        if (!$observername) {
            unset(self::$_subscribers[$pattern]);
        } elseif (is_array($observername)) {
            foreach ($observername as $value) {
                self::disable($pattern, $value);
            }
        } elseif (isset(self::$_subscribers[$pattern]) && isset(self::$_subscribers[$pattern][$observername])) {
            unset(self::$_subscribers[$pattern][$observername]);
        }
    }

}
