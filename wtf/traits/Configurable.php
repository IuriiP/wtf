<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Wtf\Traits;

/**
 * Implementation of Wtf\Interfaces\Configurable
 *
 * @author IuriiP <hardwork.mouse@gmail.com>
 */
trait Configurable {

	static protected $s_config = null;

	public static function configure($name = null) {
		if(!static::$s_config) {
			if(!$name) {
				$name = \Wtf\Helper\Common::snakeCase((new \ReflectionClass(static::class))->getShortName());
			}
			$cfg = \Wtf\Core\Config::singleton();
			$base = $cfg[$name];
			static::$s_config = (is_string($base) || $base instanceof \Wtf\Interfaces\Resource) ? new \Wtf\Core\Config($base) : $base;
			/**
			 * If is first configured and method 'configured' exists
			 */
			if(method_exists(static::class, 'configured')) {
				static::configured(static::$s_config);
			}
		}
		return static::$s_config;
	}

	public function config($path = null) {
		$config = self::configure();
		if($config) {
			return $path ? $config[$path] : null;
		}
		return null;
	}

}
