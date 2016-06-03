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
 * The adaptive Builder.
 * 
 * Descendants must defines the static methods `guess[_type0[_type1[_type2[...]]]]`
 * for building itself by the arguments types accordingly.
 * Types are defined by the function `gettype()`
 * 
 * EG:
 * guess_string
 * guess_array_string
 * etc.
 *
 * @author Iurii Prudius <hardwork.mouse@gmail.com>
 */
trait AdaptiveFactory {

    /**
     * Build the object of some class
     * dependent of the arguments list
     * 
     * @return Object
     */
    final static public function produce() {
        $args = func_get_args();
        $types = ['guess'];
        foreach ($args as $arg) {
            $types[] = gettype($arg);
        }
        while ($types) {
            $method = implode('_', $types);
            try {
                return call_user_func_array([__CLASS__, $method], $args);
            } catch (Exception $exc) {
                trigger_error(__CLASS__ . "::Builder: not found method '{$method}'");
            }
            array_pop($types);
        }
        return null;
    }

    /**
     * Predefined basic guesser
     * Just try create instance of the class
     * 
     * @return \static
     */
    static protected function guess() {
        try {
            return new static();
        } catch (\Exception $exc) {
            trigger_error(__CLASS__ . '::Builder: guess error: ' . $exc->getMessage());
        }
        return null;
    }

}
