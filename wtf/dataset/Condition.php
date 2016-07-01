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

/**
 * Description of Condition
 *
 * @author IuriiP <hardwork.mouse@gmail.com>
 */
class Condition implements \Iterator{

    private $_glue = null;
    private $_list = [];

    public function __construct($glue = 'and') {
        $this->_glue = $glue;
    }

    public function append($first, $second, $third = null) {
        if (!$third) {
            $this->_list[] = [ $first, '=', $second];
        } else {
            $this->_list[] = [ $first, $second, $third];
        }
        return $this;
    }

    public function current() {
        return current($this->_list);
    }

    public function key() {
        return key($this->_list);
    }

    public function next() {
        next($this->_list);
    }

    public function rewind() {
        reset($this->_list);
    }

    public function valid() {
        return null !== key($this->_list);
    }

}
