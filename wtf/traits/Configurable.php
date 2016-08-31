<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Wtf\Traits;

/**
 * Description of Configurable
 *
 * @author IuriiP <hardwork.mouse@gmail.com>
 */
trait Configurable {

	static protected $s_config = null;

//	protected $_config = null;

	public static function configure($name = null) {
		if(!static::$s_config) {
			if(!$name) {
				$name = \Wtf\Helper\Common::snakeCase((new \ReflectionClass(get_called_class()))->getShortName());
			}
			$cfg = \Wtf\Core\Config::singleton();
			$base = $cfg[$name];
			static::$s_config = (is_string($base) || $base instanceof \Wtf\Core\Resource) ? new \Wtf\Core\Config($base) : $base;
		}
		return static::$s_config;
	}

	public function config($path = null) {
		if($config = self::configure()) {
			return $path ? $config[$path] : null;
		}
		return null;
	}

}
