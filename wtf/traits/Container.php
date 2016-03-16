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

    protected static function _parseComplex($offset) {
        if (is_string($offset) && (FALSE !== strpos($offset, '.'))) {
            return explode('.', $offset);
        }
        return null;
    }

    protected static function _complexCheck($container, $param) {
        $offset = array_shift($param);
        if (isset($container[$offset])) {
            if ($param) {
                return self::_complexCheck($container[$offset], $param);
            }
            return true;
        }
        return false;
    }

    protected static function _complexGet($container, $param) {
        $offset = array_shift($param);
        if (isset($container[$offset])) {
            if ($param) {
                return self::_complexGet($container[$offset], $param);
            }
            return $container[$offset];
        }
        return null;
    }

    protected static function _complexSet(&$container, $param, $value) {
        $offset = array_shift($param);
        if ($param) {
            if (!isset($container[$offset])) {
                $container[$offset] = array();
            }
            return self::_complexSet($container[$offset], $param, $value);
        }
        return $container[$offset] = $value;
    }

    protected static function _complexUnset(&$container, $param) {
        $offset = array_shift($param);
        if ($param) {
            if (isset($container[$offset])) {
                self::_complexUnset($container[$offset], $param);
            }
            return;
        }
        unset($container[$offset]);
    }

    public function offsetExists($offset) {
        if ($complex = self::_parseComplex($offset)) {
            return self::_complexCheck($this->_container, $complex);
        }
        return isset($this->_container[strtolower($offset)]);
    }

    public function offsetGet($offset) {
        if ($complex = self::_parseComplex($offset)) {
            return self::_complexGet($this->_container, $complex);
        }
        return $this->_container[strtolower($offset)];
    }

    public function offsetSet($offset, $value) {
        if ($complex = self::_parseComplex($offset)) {
            return self::_complexSet($this->_container, $complex, $value);
        }
        return $this->_container[strtolower($offset)] = $value;
    }

    public function offsetUnset($offset) {
        if ($complex = self::_parseComplex($offset)) {
            return self::_complexUnset($this->_container, $complex);
        }
        unset($this->_container[strtolower($offset)]);
    }

    public function getIterator() {
        return $this->_container;
    }

    public function eliminate($offset, $def = null) {
        $elem = $this->get($offset, $def);
        $this->offsetUnset($offset);
        return $elem;
    }

    public function get($name, $def = null) {
        if ($complex = self::_parseComplex($offset)) {
            return self::_complexExists($this->_container, $complex) ?
                    self::_complexGet($this->_container, $complex) :
                    $def;
        }
        return $this->offsetExists($name) ? $this->offsetGet($name) : $def;
    }

    public function set(array $array) {
        $this->_container = [];
        foreach ($array as $key => $value) {
            $this->offsetSet($key, $value);
        }
        return $this->_container;
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

    static public function __callStatic($offset, $args = []) {
        $class = get_called_class();
        if (is_subclass_of(get_called_class(), 'Wtf\\Interfaces\\Singleton')) {
            return call_user_func_array([static::singleton(), $offset], $args);
        }
        trigger_error(__CLASS__ . '::Container: static calling accepted by Singleton only.');
        return null;
    }

}
