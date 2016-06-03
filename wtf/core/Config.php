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

use Wtf\Core\App,
    Wtf\Core\Resource;

/**
 * General Config access Class
 * 
 * @interface Container
 * @interface Singleton
 *
 * @author Iurii Prudius <hardwork.mouse@gmail.com>
 */
class Config extends ConfigBranch implements \Wtf\Interfaces\Singleton {

    use \Wtf\Traits\Singleton;

    private $_cfgRoot = null;

    private function __construct() {
        $this->_cfgRoot = App::get('path')->config;

        $list = $this->_cfgRoot->get();
        foreach ($list as $file) {
            $this->offsetSet(pathinfo($file, PATHINFO_FILENAME), Resource::produce($this->_cfgRoot, $file));
        }
        
        if($bootstrap=$this['bootstrap']) {
            foreach($bootstrap as $action) {
                if(is_callable([$action,'bootstrap'])) {
                    call_user_func([$action,'bootstrap']);
                }
            }
        }
    }

}
