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
namespace Wtf\Core\Resources;

/**
 * Description of File
 *
 * @author Iurii Prudius <hardwork.mouse@gmail.com>
 */
class None extends \Wtf\Core\Resource implements \Wtf\Interfaces\Writable {

    private $_origin = null;

    /**
     */
    public function __construct() {
        $this->_origin = '';
    }

    /**
     * @return FALSE
     */
    public function isContainer() {
        return FALSE;
    }

    /**
     * @return null
     */
    public function getScheme() {
        return null;
    }

    /**
     * @return null
     */
    public function child() {
        return null;
    }

    /**
     * @return null
     */
    public function container() {
        return null;
    }

    /**
     * @return int
     */
    public function getTime() {
        return time();
    }

    /**
     * @return string
     */
    public function getPath() {
        return '';
    }

    /**
     * @return string
     */
    public function getName() {
        return '';
    }

    /**
     * @return string
     */
    public function getType() {
            return '';
    }

    /**
     * @return string
     */
    public function getMime() {
        return '';
    }

    public function getLength() {
        
    }

    /**
     * @return []
     */
    public function get() {
        return [];
    }

    /**
     * @return null
     */
    public function getContent() {
        return null;
    }

    /**
     * @return null
     */
    public function append() {
        return null;
    }

    /**
     * @return null
     */
    public function put() {
        return null;
    }

    /**
     * @return null
     */
    public function remove() {
        return null;
    }

}
