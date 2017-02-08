<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Wtf\Interfaces;

/**
 * Pool of named instances.
 *
 * @author IuriiP
 */
interface Pool {

	public static function instance($name);
}
