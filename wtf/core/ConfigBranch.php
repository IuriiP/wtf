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

use Wtf\Core\Resource;

/**
 * Description of ConfigBase
 *
 * @author Iurii Prudius <hardwork.mouse@gmail.com>
 */
class ConfigBranch implements \Wtf\Interfaces\Container {

    use \Wtf\Traits\Container;

    public function offsetGet($offset) {
        $cfg = $this->_container[strtolower($offset)];
        if ($cfg instanceof Resource) {
            // not loaded config
            $this->offsetSet($offset, $cfg = $this->load($cfg));
        }
        return $cfg;
    }

    protected function load(Resource $res) {
        $ext = pathinfo($file = $res->getRealPath(), PATHINFO_EXTENSION);
        switch ($ext) {
            case 'cfg':
            case 'php':
                // PHP as array
                return (array) \Wtf\Core\includeFile($file);
            case 'json':
                // JSON object as array
                return json_decode($res->getContent(), true);
            case 'ini':
                // INI array
                return parse_ini_string($res->getContent(), true);
            case 'xml':
                // XML as array
                return json_decode(json_encode(simplexml_load_string($res->get_content())), true);
            default:
                // unknown format
                return null;
        }
    }

}

if (!function_exists('icludeFile')) {

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