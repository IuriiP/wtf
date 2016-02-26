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
namespace Wtf\Interfaces;

/**
 *
 * @author Iurii Prudius <hardwork.mouse@gmail.com>
 */
interface Resource {

    /**
     * Check if a container
     * 
     * @return boolean
     */
    public function isContainer();

    /**
     * Get the named child
     * 
     * @param type $name
     * @return type
     */
    public function child($name);

    /**
     * Get the object's container
     * 
     * @return \Wtf\Core\Resource
     */
    public function container();

    /**
     * Get the specified timestamp of resource:
     * 'c' - create time
     * 'm' - modify time
     * 'a' - access time (default)
     * 
     * @param string $type
     * @return int
     */
    public function getTime($type = null);

    /**
     * Get the scheme of the object
     * 
     * @return string
     */
    public function getScheme();

    /**
     * Get the full path to the object
     * 
     * @return string
     */
    public function getPath();

    /**
     * Get the object self name
     * 
     * @return string
     */
    public function getName();

    /**
     * Get the object type
     * 
     * @return string
     */
    public function getType();

    /**
     * Get the object mime-type
     * 
     * @return string
     */
    public function getMime();
}
