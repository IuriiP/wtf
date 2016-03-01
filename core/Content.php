<?php
/*
 * Copyright (C) 2016 Iurii Prudius <hardwork.mouse@gmail.com>
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
namespace Wtf\Core;

/**
 * Description of Content
 *
 * @author Iurii Prudius <hardwork.mouse@gmail.com>
 */
abstract class Content implements \Wtf\Interfaces\Factory {

    use \Wtf\Traits\Factory;

    const
            ASSERT_END = 0,
            ASSERT_BEGIN = 1,
            ASSERT_HERE = 2,
            ASSERT_ONLOAD = 3;

    /**
     * @var string type of content
     */
    protected $type = null;

    /**
     * @var mixed content
     */
    protected $content = null;

    /**
     * Check type
     * 
     * @param string $type
     * @return boolean
     */
    final public function isType($type) {
        return strcasecmp($this->type, $type) === 0;
    }

    /**
     * Get data mime-type for the 'Content-type' header
     * 
     * @return string 
     */
    abstract public function getMime();

    /**
     * Get data length in bytes for the 'Content-length' header
     * 
     * @return int
     */
    abstract public function getLength();

    /**
     * Append data to content
     * 
     * @return boolean is success
     */
    abstract public function append($args);

    /**
     * Send data to the output stream
     * 
     * @return boolean is success
     */
    abstract public function send();
}
