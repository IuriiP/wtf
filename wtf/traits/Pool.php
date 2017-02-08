<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Wtf\Traits;

/**
 * Implementation of Pool
 *
 * @author IuriiP
 */
trait Pool {

	static private $_pool = [];

	/**
	 * If descendant is registered - returns it.
	 * If not registered - create and register new descendant.
	 * 
	 * @param string $name
	 * @return static
	 */
	public static function instance($name = '') {
		if(!isset(self::$_pool[$name])) {
			$class = new \ReflectionClass(static::class);
			if($class->implementsInterface(\Wtf\Interfaces\Factory::class)) {
				return self::$_pool[$name] = static::factory($class->getName(), [$name]);
			}
			return self::$_pool[$name] = new static($name);
		}
		return self::$_pool[$name];
	}

}
