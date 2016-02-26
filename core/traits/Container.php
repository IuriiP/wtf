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
namespace Wtf\Traits;

/**
 * Container functionality for Wtf\Interfaces\Container
 * 
 * Supports access:
 * - Iterator
 * - $container[$key] : \ArrayAccess
 * - $container($key) : __invoke
 * - $container->$key : __get, __set
 * - $container->$key() : __call
 *
 * @author Iurii Prudius <hardwork.mouse@gmail.com>
 */
trait Container {

    protected $_container = [];

    public function offsetExists($offset) {
        return isset($this->_container[strtolower($offset)]);
    }

    public function offsetGet($offset) {
        return $this->_container[strtolower($offset)];
    }

    public function offsetSet($offset, $value) {
        return $this->_container[strtolower($offset)] = $value;
    }

    public function offsetUnset($offset) {
        unset($this->_container[strtolower($offset)]);
    }

    public function getIterator() {
        return $this->_container;
    }

    public function get($name, $def = null) {
        return $this->offsetExists($name) ? $this->offsetGet($name) : $def;
    }

    public function getOnly($only = []) {
        if ($only) {
            return array_intersect_key($this->_container, array_change_key_case(array_fill_keys($only, 0)));
        }
    }

    public function getExcept($except = []) {
        return array_diff_key($this->_container, array_change_key_case(array_fill_keys($except, 0)));
    }
    
    public function set(array $array) {
        return $this->_container = array_replace_recursive($this->_container, array_change_key_case($array));
    }

    public function __get($offset) {
        return $this->offsetExists($offset) ? $this->offsetGet($offset) : null;
    }

    public function __set($offset, $value) {
        return $this->offsetSet($offset, $value);
    }

    public function __invoke() {
        $args = func_get_args();
        if (count($args)) {
            $offset = array_shift($args);
            return $this->__call($offset, $args);
        }
        return $this;
    }

    public function __call($offset, $args = []) {
        if (($elem = $this->__get($offset)) && count($args)) {
            if (is_object($elem)) {
                $obj = new \ReflectionObject($elem);
                if ($obj->hasMethod('__invoke')) {
                    $ref = $obj->getMethod('__invoke');
                    return $ref->invokeArgs($elem, $args);
                }
            } elseif (is_callable($elem)) {
                return call_user_func_array($elem, $args);
            } else {
                trigger_error(__CLASS__ . '::Container: incorrect invoking.');
            }
        }
        return $elem;
    }
    
    public function __callStatic($offset, $args = []) {
        $class = get_called_class();
        if(is_subclass_of(get_called_class(), 'Wtf\\Interfaces\\Singleton')) {
            return call_user_func_array([static::singleton(),$offset], $args);
        }
        trigger_error(__CLASS__.'::Container: static call to not the Singleton.');
    }

}
