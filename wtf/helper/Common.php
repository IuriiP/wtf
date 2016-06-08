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

namespace Wtf\Helper;

/**
 * Description of Common
 *
 * @author IuriiP <hardwork.mouse@gmail.com>
 */
class Common {
    //put your code here
}

if (!function_exists('includeFile')) {

    /**
     * Isolated file including
     * 
     * @param string $fname
     * @return mixed
     */
    function includeFile($fname) {
        return include($fname);
    }

}
