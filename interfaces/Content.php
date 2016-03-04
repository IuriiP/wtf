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
namespace Wtf\Interfaces;

/**
 * Universal Content prototype
 *
 * @author Iurii Prudius <hardwork.mouse@gmail.com>
 */
interface Content
{
    const
            INJECT_HERE = 0,
            INJECT_BEGIN = 1,
            INJECT_END = 2;


    /**
     * Get the private method name for injecting
     * 
     * @param string $type
     * @return string|false
     */
    public function canInject($type);

    /**
     * Common injection entry point 
     * 
     * @param array $asset
     */
    public function inject($asset);

    /**
     * Get data mime-type for the 'Content-type' header
     * 
     * @return string 
     */
    public function getMime();

    /**
     * Get data length in bytes for the 'Content-length' header
     * 
     * @return int
     */
    public function getLength();

    /**
     * Shortcut for the fragment injecting to the end
     * 
     * @param type $content
     * @return type
     */
    public function append($content);

}
