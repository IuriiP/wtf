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

namespace Wtf\Core;

use Wtf\Core\Resource;

/**
 * Description of Cache
 *
 * @author IuriiP <hardwork.mouse@gmail.com>
 */
class Cache implements \Wtf\Interfaces\Configurable, \Wtf\Interfaces\Singleton {
    
    use \Wtf\Traits\Configurable,
            \Wtf\Traits\Singleton;
    
    /**
     * @var Wtf\Core\Resource
     */
    static private $_resource = null;

    private function __construct() {
        static::$_resource = Resource::produce($this->config('resource'),  $this->config('options'));
    }

    /**
     * Get or create cached resource.
     * 
     * @param \Wtf\Core\Resource $resource
     * @param \Callback $callback gets the original resource, returns the prepared content
     * @return string
     */
    public function __invoke(Resource $resource, $callback=null) {
        if(static::$_resource) {
            // algo = md4 default as the fastest one
            $known=static::$_resource->child(hash($this->config('algorithm') ? : 'md4', $resource->getPath().'+'.$resource->getData()));
            if($known->exists() && ($known->getTime()>=$resource->getTime())) {
                   return $known->getContent();
            }
            // make/rebuild cache
            return $known->put(($callback && is_callable($callback)) ? $callback($resource) : $resource->getContent())->getContent();
        }
        return $resource->getContent();
    }
    
    /**
     * Static invoking.
     * 
     * @param Resource $resource
     * @param Callback $callback gets the original resource, returns the prepared content
     * @return string
     */
    static public function supply(Resource $resource, $callback=null) {
        $self = static::singleton();
        return $self($resource,$callback);
    }
    
}
