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
 * Description of Entity
 *
 * @author Iurii Prudius <hardwork.mouse@gmail.com>
 */
abstract class Entity implements \Wtf\Interfaces\Factory {

	use \Wtf\Traits\Factory;

	/**
	 * @var string type of content
	 */
	protected $type = null;

	/**
	 * @var Object
	 */
	protected $content = null;

	/**
	 * @var array options for processors
	 */
	protected $options = [];

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
	public function __construct($content = null, $type = '') {
		$this->content = $content;
		$this->type = strtolower($type);
	}

	/**
	 * Check type
	 * 
	 * @param string $type
	 * @return boolean
	 */
	final public function isType($type) {
		return strcasecmp($this->type, $type) === 0;
	}

	/**
	 * Shortcut to isType()
	 * 
	 * @param string $type
	 * @return boolean
	 */
	final public function type($type) {
		return $this->isType($type);
	}

	/**
	 * Set/get content
	 * 
	 * @param \Wtf\Interfaces\Content $content
	 * @return mixed
	 */
	final public function content(\Wtf\Interfaces\Content $content = null) {
		if($content) {
			$this->content = $content;
		}
		return $this->content;
	}

	/**
	 * Magic to get content
	 *
	 * @return string 
	 */
	abstract public function __toString();

	/**
	 * Apply processor to this object.
	 * 
	 * @param \Wtf\Core\Processor $processor
	 * @return $this
	 */
	final public function apply(Processor $processor) {
		return $processor->process($this);
	}

	/**
	 * Get/set children array
	 * 
	 * @param type $children
	 * @return Object
	 */
	final public function children($children = null) {
		if($children !== null) {
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
	final public function addChild($children) {
		if($children) {
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
	final public function hasChild($name) {
		return (boolean) $name && isset($this->children[$name]);
	}

}
