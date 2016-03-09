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
namespace Wtf\Traits;

/**
 * Universal Content prototype
 *
 * @author Iurii Prudius <hardwork.mouse@gmail.com>
 */
trait Content
{

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
     * @return $this
     */
    final public function inject($asset)
    {
        if ($method = $this->canInject($asset['content']->getType())) {
            $this->$method($asset['content'], $asset['position']);
        }
        return $this;
    }

    /**
     * Shortcut for the fragment injecting to the end
     * 
     * @param mixed $content
     * @return $this
     */
    final public function append($content)
    {
        $this->inject([
                    'content' => static::make($this->type, $content),
                    'position' => \Wtf\Interfaces\Content::INJECT_END,
        ]);
        return $this;
    }

    /**
     * Content postprocessing directive
     * 
     * @param \Wtf\Core\Processor $processor
     * @param array $data
     * @return $this
     */
    final public function apply($processor, $data = [])
    {
        $this->inject([
                    'content' => $processor,
                    'position' => \Wtf\Interfaces\Content::INJECT_END,
                    'data' => $data,
        ]);
        return $this;
    }

}
