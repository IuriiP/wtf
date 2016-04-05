<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Wtf\Core;

/**
 * Common Database incapsulation & interface.
 * 
 * Using (over Pool):
 *  $db_local = Database::instance('local');
 *  $db_logs = Database::instance('logs');
 *  $db_cloud = Database::instance('cloud');
 *
 * @author iprudius
 */
class Database implements \Wtf\Interfaces\Pool {

    use \Wtf\Traits\Pool;

    protected $_id = null;
    protected $_engine = null;

    private function __construct($name) {
        $this->$_id = $name;
        $this->$_engine = self::factory(['', $name]);
    }

}
