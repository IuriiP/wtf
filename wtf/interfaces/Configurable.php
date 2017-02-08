<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Wtf\Interfaces;

/**
 * It's for objects which have a config.
 *
 * @author IuriiP <hardwork.mouse@gmail.com>
 */
interface Configurable {

	public static function configure($name = null);

	public function config($path = null);
}
