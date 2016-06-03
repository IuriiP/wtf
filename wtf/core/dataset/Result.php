<?php

/*
 * Copyright (C) 2016 IuriiP <hardwork.mouse@gmail.com>
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

namespace Wtf\Dataset;

use Wtf\Dataset\Error,
    Wtf\Dataset\Paginator;

/**
 * Database Result
 *
 * @author IuriiP <hardwork.mouse@gmail.com>
 */
class Result implements \Iterator, \Countable {

    /**
     * @var \Wtf\Database\Error 
     */
    private $_status = null;

    /**
     * @var array
     */
    private $_result = [];
    private $_count = 0;
    private $_paginator = null;

    /**
     * Prepare result & error
     * 
     * @param variant $data
     * @param \Wtf\Dataset\Error $error
     */
    public function __construct($data, $error = null) {
        $this->_status = $error? : new Error(Error::SUCCESS);
        if (is_array($data)) {
            $this->_result = $data;
            $this->_count = count($data);
        } elseif (is_int($data)) {
            $this->_count = $data;
        }
        $this->_paginator = new Paginator($this->_count, 0, $this->_count);
    }

    public function paginate($size, $page, $total) {
        $this->_paginator = new Paginator($size, $page, $total);
        return $this;
    }

    public function paginator() {
        return $this->_paginator;
    }

    public function count() {
        return $this->_count;
    }

    public function __get($name) {
        if (($current = current($this->_result)) && (isset($current[$name]))) {
            return $current[$name];
        }
        return null;
    }

    public function current() {
        return current($this->_result);
    }

    public function key() {
        return key($this->_result);
    }

    public function next() {
        return next($this->_result);
    }

    public function rewind() {
        reset($this->_result);
    }

    public function valid() {
        return false !== current($this->_result);
    }

}
