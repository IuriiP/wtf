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
class Blade extends \Wtf\Core\Compiler
{
    static private $_commands = [
        '~@(extends)\s*\((.+)\)~',
        '~@(section)\s*\((.+)\)~',
        '~@(stop)~',
        '~@(show)~',
        '~@(parent)~',
        '~@(yield)\s*\((.+)\)~',
        '~@(include)\s*\((.+)\)~',
    ];

    static private $_templates = [
        '~@\{\{(.*)\}\}~'=>'{{$1}}', // @{{...}}
        '~\{\{--.*--\}\}~'=>'', // {{--...--}}
        '~\{\{(.*)\}\}~'=>'<?php echo htmlspecialchars($1); ?>', // {{...}}
        '~\{!!(.*)!!\}~'=>'<?php echo $1; ?>', // {!!...!!}
        '~@if(.*)$~'=>'<?php if $1: ?>', // @if
        '~@else(.*)$~'=>'<?php else$1: ?>', // @else | @elseif
        '~@endif(.*)$~'=>'<?php endif; ?>', // @endif
        '~@unless(.*)$~'=>'<?php if(!$1): ?>', // @unless
        '~@endunless(.*)$~'=>'<?php endif; ?>', // @endunless
        '~@foreach(.*)$~'=>'<?php foreach$1: ?>', // @foreach
        '~@endforeach(.*)$~'=>'<?php endforeach; ?>', // @endforeach
        '~@for(.*)$~'=>'<?php for$1: ?>', // @for
        '~@continue(.*)$~'=>'<?php continue; ?>', // @continue 
        '~@endfor(.*)$~'=>'<?php endfor; ?>', // @endfor
        '~@while(.*)$~'=>'<?php while$1: ?>', // @while
        '~@endwhile(.*)$~'=>'<?php endwhile; ?>', // @endwhile
        '~@forelse(\s*\(\s*(.+)\s+.*)$~'=>'<?php if($2) foreach$1: ?>', // @forelse
        '~@empty(.*)$~'=>'<?php endforeach; else: ?>', // @empty
        '~@endforelse(.*)$~'=>'<?php endif; ?>', // @endforelse
        '~@switch(.*)$~'=>'<?php switch$1: ?>', // @switch
        '~^/s*@case(.*)$~'=>'<?php case$1: ?>', // @case
        '~@break(.*)$~'=>'<?php break; ?>', // @break 
        '~@endswitch(.*)$~'=>'<?php endswitch; ?>', // @endswitch
        '~@(lang)\s*\((.+)\)~'=>'<?php App::language("translate",$2); ?>',
        '~@(choice)\s*\((.+)\)~'=>'<?php App::language("choice",$2); ?>',
    ];
    
    public function process($content)
    {
        $content = preg_replace_callback(self::$_commands, [$this,'execute'], $content);
        return preg_replace(array_keys(self::$_templates), self::$_templates, $content);
    }

    protected function execute($matches)
    {
        $keyword = $matches[1];
        $method = "do_{$keyword}"; 
        if(method_exists($this, $method)) {
            return $this->$method($matches);
        }
        return '';
    }
    
    protected function do_extends($param)
    {
        return ;
    }
    protected function do_section($param)
    {
        return ;
    }
    protected function do_stop($param)
    {
        return ;
    }
    protected function do_show($param)
    {
        return ;
    }
    protected function do_parent($param)
    {
        return ;
    }
    protected function do_yield($param)
    {
        return ;
    }
    protected function do_include($param)
    {
        return ;
    }
}
