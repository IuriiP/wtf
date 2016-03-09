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
 * Description of Entity
 *
 * @author Iurii Prudius <hardwork.mouse@gmail.com>
 */
abstract class Entity implements \Wtf\Interfaces\Factory
{

    use \Wtf\Traits\Factory;

    /**
     * @var string type of content
     */
    protected $type = null;

    /**
     * @var Object
     */
    protected $content = null;

    /**
     * Check type
     * 
     * @param string $type
     * @return boolean
     */
    final public function isType($type)
    {
        return strcasecmp($this->type, $type) === 0;
    }

    /**
     * Shortcut to isType()
     * 
     * @param string $type
     * @return boolean
     */
    final public function type($type)
    {
        return $this->isType($type);
    }

    /**
     * Set/get content
     * 
     * @param mixed $content
     * @return mixed
     */
    public function content($content = null)
    {
        if ($content) {
            $this->content = $content;
        }
        return $this->content;
    }

    /**
     * 
     * @param mixed $content
     * @param string $type
     */
    public function __construct($content = null, $type = null)
    {
        $this->content = $content;
        $this->type = $type? : __CLASS__;
    }

    /**
     * Magic to get content
     *
     * @return string 
     */
    abstract public function __toString();
    
    /**
     * Apply processor to this object.
     * 
     * @param \Wtf\Core\Processor $processor
     * @return $this
     */
    final public function apply(Processor $processor)
    {
        return $processor->process($this);
    }
}
