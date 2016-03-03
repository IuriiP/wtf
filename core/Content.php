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
 * Universal Content prototype
 *
 * @author Iurii Prudius <hardwork.mouse@gmail.com>
 */
abstract class Content extends \Wtf\Core\Entity
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
    final public function canInject($type)
    {
        $method = "inject_{$type}";
        if (method_exists($this, $method)) {
            return $method;
        }
        return false;
    }

    /**
     * Common injection entry point 
     * 
     * @param array $asset
     */
    final public function inject($asset)
    {
        if ($method = $this->canInject($asset['content']->getType())) {
            return $this->$method($asset['content'], $asset['position']);
        }
        return false;
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
     * Shortcut for the fragment injecting to the end
     * 
     * @param type $content
     * @return type
     */
    final public function append($content)
    {
        return $this->inject([
                    'content' => self::make($this->type, $content),
                    'position' => self::INJECT_END,
        ]);
    }

}
