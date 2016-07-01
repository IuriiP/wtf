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
 * Description of Paginator
 *
 * @author IuriiP <hardwork.mouse@gmail.com>
 */
class Paginator {

    private $_size = 0;
    private $_page = 0;
    private $_total = 0;

    public function __construct($size, $page, $total) {
        $this->_size = $size;
        $this->_page = $page - 1;
        $this->_total = $total;
    }

    public function __get($name) {
        switch ($name) {
            case 'size': return $this->_size;
            case 'page': return $this->_page + 1;
            case 'total': return $this->_total;
            case 'pages': return $this->_total > 0 ? (int) (($this->_total - 1) / $this->_size) + 1 : 0;
            case 'first': return $this->_total > 0 ? $this->_size * $this->_page + 1 : 0;
            case 'last': return $this->_total > 0 ? min($this->_size * ($this->_page + 1), $this->_total) : 0;
        }
        return 0;
    }

}
