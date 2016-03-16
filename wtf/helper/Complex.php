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
namespace Wtf\Helper;

/**
 * Abstract static class provide a lot of functions
 * for manipulations with the arrays and objects.
 *
 * @author Iurii Prudius <hardwork.mouse@gmail.com>
 */
abstract class Complex
{

    /**
     * Exclude the blacklisted keys
     * 
     * @param array $array Source array
     * @param array $black Array of the blacklisted keys
     * @return array Cleared array
     */
    static public function except($array, $black)
    {
        return array_diff_key($array, array_flip($black));
    }

    /**
     * Include the whitelisted keys only
     * 
     * @param array $array Source array
     * @param array $white Array of the whitelisted keys
     * @return array Cleared array
     */
    static public function only($array, $white)
    {
        return array_intersect_key($array, array_flip($white));
    }

    /**
     * Object to Array
     *
     *  @param object $obj
     *  @param array|boolean $remove Remove the array specified keys or '@attributes' if true
     *  @return array
     */
    static public function obj2arr($obj, $remove = null)
    {
        if (is_object($obj)) {
            $elem = (array) $obj;
        } else {
            $elem = $obj;
        }
        if ($remove && is_bool($remove)) {
            $remove = ['@attributes'];
        }
        if (is_array($elem)) {
            return array_map(__METHOD__, $remove ? self::arrExcept($elem, $remove) : $elem);
        }
        return $elem;
    }

    /**
     * Array to Object
     *
     * @param array $arr  
     * @return object
     */
    static public function arr2obj($arr)
    {
        return json_decode(json_encode($arr));
    }

    /**
     * Array to .INI formatted text
     *
     * @parameter array $a  
     * @parameter array $parent  
     * @return string
     */
    static public function arr2ini(array $a, array $parent = array())
    {
        $out = array();
        foreach ($a as $k => $v) {
            if (is_array($v)) {
                $parent[] = $k;
                $out[] = '[' . join('.', $parent) . ']' . PHP_EOL;
                $out[] = arr2ini($v, $parent);
            } else {
                $out[] = "$k=$v";
            }
        }
        return implode(PHP_EOL, $out);
    }

    /**
     * .INI formatted text to Array
     *
     * @param string $ini
     * @return array
     */
    static public function ini2arr($ini)
    {
        return parse_ini_string($ini, true);
    }

    /**
     * Get named value from array or default
     *
     * @param mixed $from Array or Object
     * @param string $key 
     * @param mixed $dflt Default value
     * @return mixed
     */
    static public function get($from, $key, $dflt = null)
    {
        $afrom = (array) $from;
        if (isset($afrom[$key])) {
            return $afrom[$key];
        }
        return $dflt;
    }

    /**
     * Get named value from array or default and unset it
     *
     * @param mixed $from Array or Object
     * @param string $key 
     * @param mixed $dflt
     * @return mixed
     */
    static public function eliminate(&$from, $key, $dflt = null)
    {
        $afrom = (array) $from;
        if (isset($afrom[$key])) {
            $ret = $afrom[$key];
            unset($from[$key]);
            return $ret;
        }
        return $dflt;
    }

    /**
     * Get attributes of XML node as array.
     * 
     * @param mixed $el DOMNode or SimpleXMLElement 
     * @return array|null
     */
    static public function attr2arr($el)
    {
        if ($el instanceof DOMNode) {
            if (!$el->attributes) {
                return null;
            }
            $xml = simplexml_import_dom($el);
        } elseif (!($xml = $el) || !($xml instanceof SimpleXMLElement)) {
            return null;
        }
        $atts = (array) $xml->attributes();
        return isset($atts['@attributes']) ? $atts['@attributes'] : null;
    }

}
