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
namespace Wtf\Core;

/**
 * Aggregate some object and (un)named children
 *
 * @author Iurii Prudius <hardwork.mouse@gmail.com>
 */
abstract class Aggregator extends Entity implements \IteratorAggregate
{

    /**
     * @var array of dependent Objects
     */
    protected $children = [];

    /**
     * Create instance
     * 
     * @param Object $content
     * @param array $children
     */
    public function __construct($content = null, $children = [])
    {
        parent::__construct($content);

        $this->children = (array) $children;
    }

    /**
     * Implements \IteratorAggregate
     * 
     * @return array
     */
    final public function getIterator()
    {
        return $this->children;
    }

    /**
     * Get/set content Object
     * 
     * @param Object|null $content
     * @return Object
     */
    final public function content($content = null)
    {
        if ($content && $content instanceof Content) {
            $this->content = $content;
        }
        return $this->content;
    }

    /**
     * Get/set children array
     * 
     * @param type $children
     * @return Object
     */
    final public function children($children = null)
    {
        if ($children !== null) {
            $this->children = (array) $children;
        }
        return $this->children;
    }

    /**
     * Append new children.
     * Not replace existed!
     * 
     * @param array $children
     * @return array
     */
    final public function addChild($children)
    {
        if ($children) {
            $this->children = array_merge((array) $children, $this->children);
        }
        return $this->children;
    }

    /**
     * Check if child exists
     * 
     * @param string $name
     * @return boolean
     */
    final public function hasChild($name)
    {
        return (boolean) $name && isset($this->children[$name]);
    }

}
