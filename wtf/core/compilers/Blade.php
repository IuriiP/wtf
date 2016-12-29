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

namespace Wtf\Core\Compilers;

/**
 * Laravel's 'Blade' syntax compiler
 *
 * @author Iurii Prudius <hardwork.mouse@gmail.com>
 */
class Blade extends \Wtf\Core\Compiler {

	/**
	 * Pairs of pattern => closure
	 * 
	 * @var array
	 */
	static private $_commands = [
		'~@(extends)\s*\((.+)\)~' => [__CLASS__,'do_include'],
		'~@(yield)\s*\((.+)\)~' => [__CLASS__,'do_yield'],
		'~@(section)\s*\((.+)\)(.*)@(endsection|stop|show|append|overwrite)~' => [__CLASS__,'do_section'],
		'~@(include)\s*\((.+)\)~'=>[__CLASS__,'do_include'],
	];

	/**
	 * Pairs of pattern => replace
	 *
	 * @var type 
	 */
	static private $_templates = [
		'~@\{\{(.*)\}\}~' => '{{$1}}', // @{{...}}
		'~\{\{--.*--\}\}~' => '', // {{--...--}}
		'~\{\{(.*)\}\}~' => '<?php echo htmlspecialchars($1); ?>', // {{...}}
		'~\{!!(.*)!!\}~' => '<?php echo $1; ?>', // {!!...!!}
		'~@if(.*)$~' => '<?php if $1: ?>', // @if
		'~@else(.*)$~' => '<?php else$1: ?>', // @else | @elseif
		'~@endif(.*)$~' => '<?php endif; ?>', // @endif
		'~@unless(.*)$~' => '<?php if(!$1): ?>', // @unless
		'~@endunless(.*)$~' => '<?php endif; ?>', // @endunless
		'~@foreach(.*)$~' => '<?php foreach$1: ?>', // @foreach
		'~@endforeach(.*)$~' => '<?php endforeach; ?>', // @endforeach
		'~@for(.*)$~' => '<?php for$1: ?>', // @for
		'~@continue(.*)$~' => '<?php continue; ?>', // @continue 
		'~@endfor(.*)$~' => '<?php endfor; ?>', // @endfor
		'~@while(.*)$~' => '<?php while$1: ?>', // @while
		'~@endwhile(.*)$~' => '<?php endwhile; ?>', // @endwhile
		'~@forelse(\s*\(\s*(.+)\s+.*)$~' => '<?php if($2) foreach$1: ?>', // @forelse
		'~@empty(.*)$~' => '<?php endforeach; else: ?>', // @empty
		'~@endforelse(.*)$~' => '<?php endif; ?>', // @endforelse
		'~@switch(.*)$~' => '<?php switch$1: ?>', // @switch
		'~^/s*@case(.*)$~' => '<?php case$1: ?>', // @case
		'~@break(.*)$~' => '<?php break; ?>', // @break 
		'~@endswitch(.*)$~' => '<?php endswitch; ?>', // @endswitch
		'~@lang\s*\((.+)\)~' => '<?php App::language("translate",$1); ?>',
		'~@choice\s*\((.+)\)~' => '<?php App::language("choice",$1); ?>',
	];

	/**
	 * Named sections
	 * 
	 * @var array
	 */
	static protected $sections = [];

	/**
	 * The first configured execution for expanding commands and templates
	 * 
	 * @param array $config
	 */
	protected static function configured($config) {
		if(isset($config['commands'])) {
			static::$_commands = array_merge(static::$_commands,$config['commands']);
		}
		if(isset($config['templates'])) {
			static::$_templates = array_merge(static::$_templates,$config['templates']);
		}
	}

	/**
	 * Compile the content
	 * 
	 * @param string $content
	 * @return string
	 */
	public static function compile($content) {
		$content = preg_replace_callback(array_keys(self::$_commands), self::$_commands, $content);
		return preg_replace(array_keys(self::$_templates), self::$_templates, $content);
	}

	/**
	 * Get the named section content
	 * 
	 * @param string $name
	 * @return string
	 */
	public static function section($name) {
		return isset(self::$sections[$name]) ? self::$sections[$name] : null;
	}

	/**
	 * Append command as callback
	 * 
	 * @param string $pattern
	 * @param \Closure $callback
	 */
	public static function addCommand($pattern,$callback) {
		self::$_commands[$pattern] = $callback;
	}

	/**
	 * Append template as replacing string
	 * 
	 * @param string $pattern
	 * @param string $replace
	 */
	public static function addTemplate($pattern,$replace) {
		self::$_templates[$pattern] = $replace;
	}

	/**
	 * Include view
	 * 
	 * @param array $param
	 * @return string
	 */
	protected static function do_include($param) {
		if($param[2]) {
			return \Wtf\Core\View::_($param[2])->get();
		}
		return '';
	}

	/**
	 * Process the section
	 * 
	 * @param array $param
	 * @return string
	 */
	protected static function do_section($param) {
		$name = $param[2];
		$body = $param[3];
		$stop = $param[4];

		switch($stop) {
			case 'show':
				return self::$sections[$name] = self::compile($body);
			case 'append':
				self::$sections[$name] .= self::compile($body);
				break;
			default:
				self::$sections[$name] = self::compile($body);
				break;
		}
		return '';
	}

	/**
	 * Yield the section
	 * 
	 * @param array $param
	 * @return string
	 */
	protected static function do_yield($param) {
		$name = $param[2];
		if($name) {
			return '<?php echo '. static::class . "::section('{$name}');?>";
		}
		return '';
	}

}
