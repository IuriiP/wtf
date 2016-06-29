<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Wtf\Traits;

/**
 * Description of Pool
 *
 * @author IuriiP
 */
trait Pool {

	static private $_pool = [];

	public static function instance($name = '') {
		if(!isset(self::$_pool[$name])) {
			return self::$_pool[$name] = new static($name);
		}
		return self::$_pool[$name];
	}

}
