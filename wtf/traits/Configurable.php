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
    protected $_config = null;

    static public function configure($name = null) {
        if (!static::$s_config) {
            static::$s_config = \Wtf\Core\App::config($name? : (new \ReflectionClass(get_called_class()))->getShortName());
        }
        return static::$s_config;
    }

    public function config($path = null) {
        if ($config = static::configure()) {
            return $path ? $config[$path] : null;
        }
        return null;
    }

}
