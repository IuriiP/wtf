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
 * Fast access to resources which need preprocessing.
 * 
 * Config:
 * 'cache' => [
 *      'resource' => 'file:///private/cache', // path to the resource for store cached data
 *      'options' => [], // some options for using this resource
 *      'algorithm' => 'md5', // the legal algorithm for `hash`
 * ],
 * 'compilers' => [
 *      'blade' => ['\\Wtf\\Core\\Compilers','compile'], // for `*.blade` files
 * ],
 * 
 * Using:
 * $cache = Cache::singleton();
 * $data = $cache('file:///public/data/file.tpl',[TemplateCompiler,'compile']);
 * 
 * or
 * $data = Cache::supply('file:///public/data/file.tpl',[TemplateCompiler,'compile']);
 *
 * @author IuriiP <hardwork.mouse@gmail.com>
 */
class Cache implements \Wtf\Interfaces\Configurable, \Wtf\Interfaces\Singleton {

	use \Wtf\Traits\Configurable,
	 \Wtf\Traits\Singleton;

	/**
	 * @var Wtf\Core\Resource
	 */
	public static $_resource = null;

	/**
	 * Produce root resource for caching.
	 */
	private function __construct() {
		self::$_resource = Resource::produce($this->config('resource')? : \Wtf\Helper\Common::absolutePath('cache'), $this->config('options')? : []);
	}

	/**
	 * Compile source resource to target resource
	 * 
	 * @param \Wtf\Core\Resource $target
	 * @param \Wtf\Core\Resource $source
	 * @param \Closure|array $callback
	 * @return type
	 */
	private function _compile($target, $source, $callback) {
		if($callback) {
			if(is_callable($callback)) {
				return $target
						->put($callback($source))
						->getContent();
			} elseif(is_array($callback)) {
				return $target
						->put(preg_replace(array_keys($callback), array_values($callback), $source->getContent()))
						->getContent();
			}
		}
		return $known
				->put($source->getContent())
				->getContent();
	}

	/**
	 * Get or create cached resource.
	 * 
	 * @param string|\Wtf\Core\Resource $source
	 * @param \Closure|array|null $callback gets the original resource, returns the prepared content
	 * @return string
	 */
	public function __invoke($source, $callback = null) {
		$resource = Resource::produce($source);

		if(self::$_resource) {
			// algo = md4 default as the fastest one
			$known = self::$_resource->child(hash($this->config('algorithm') ? : 'md4', $resource->getPath() . '?' . $resource->getData()));
			if($known->exists() && ($known->getTime() >= $resource->getTime())) {
				return $known->getContent();
			}
			// make/rebuild cache
			return $this->_compile($known, $resource, $callback ? : Config::singleton()->get('compilers')[$resource->getType()]);
		}
		return $resource->getContent();
	}

	/**
	 * Static invoking.
	 * 
	 * @param string|Resource $source
	 * @param Callback $callback gets the original resource, returns the prepared content
	 * @param array $args parameters for preparing
	 * @return string
	 */
	public static function supply($source, $callback = null) {
		$self = self::singleton();
		return $self($source, $callback);
	}

}
