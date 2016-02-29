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
 * Factory for produce a object of the specified class
 * in specified namespace
 *
 * @author Iurii Prudius <hardwork.mouse@gmail.com>
 */
trait Factory {

    /**
     * Object Factory
     * 
     * @param mixed $named string: full class name, array: [namespace,name], object: prototype
     * @param array $args args list
     * @return mixed|null
     */
    final static public function factory($named, $args = []) {
        if (is_array($named)) {
            $class = (empty($named[0]) ? __CLASS__.'s' : $named[0]) . '\\' . ucfirst($named[1]);
        } elseif(is_string($named)) {
            $class = \Wtf\Core\App::get($named) || $named;
        } elseif(is_object($named)) {
            $class = get_class($named);
        } else {
            return null;
        }
        if(is_object($class)) {
            // it's contract for singleton
            return $class;
        }
        try {
            $ref = new \ReflectionClass($class);
            return $ref->newInstanceArgs($args);
        } catch (Exception $exc) {
            trigger_error(__CLASS__ . "::Factory: error istantiating '{$class}'");
        }
        return null;
    }

}
