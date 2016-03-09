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
namespace Wtf\Core\Entities;

/**
 * Description of Resource
 *
 * @author Iurii Prudius <hardwork.mouse@gmail.com>
 */
class Resource extends \Wtf\Core\Entity implements \Wtf\Interfaces\Content
{

    use \Wtf\Traits\Content;
    
    public function __construct($data, $opts = [])
    {
        $resource = \Wtf\Core\Resource::build($data, $opts);
        parent::__construct($resource, 'resource');
    }

    public function __toString()
    {
        if($this->content && ($this->content instanceof \Wtf\Interfaces\Readable)) {
            return $this->content->getContent();
        }
        return ''; 
    }

    public function getLength()
    {
        if($this->content && ($this->content instanceof \Wtf\Interfaces\Readable)) {
            return $this->content->getLength();
        }
        return 0;
    }

    public function getMime()
    {
        if($this->content && ($this->content instanceof \Wtf\Interfaces\Readable)) {
            return $this->content->getMime();
        }
        return 'application/octet-stream';
    }

    protected function inject_processor($processor,$position)
    {
        
    }
}
