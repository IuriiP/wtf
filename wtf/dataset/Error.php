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
 * Dataset Error
 *
 * @author IuriiP <hardwork.mouse@gmail.com>
 */
class Error {

    const SUCCESS = 0,
            WARNING = 1,
            ERROR = 2;

    public $level = 0;
    public $descriptor = null;
    public $source = null;
    public $data = null;

    /**
     * Make extended error object
     * 
     * @param int $level
     * @param string $desc
     * @param array $source
     * @param array $data
     */
    public function __construct($level, $desc=null, $source = null, $data = null) {
        $this->level = $level;
        $this->descriptor = $desc;
        $this->source = $source;
        $this->data = $data;
    }
    
    public function __toString() {
        return $this->descriptor;
    }

}
