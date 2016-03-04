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
 * Description of HttpDebug
 *
 * @author Iurii Prudius <hardwork.mouse@gmail.com>
 */
class HttpDebug extends \Wtf\Core\Entity implements \IteratorAggregate
{

    public function __construct($data = null)
    {
        parent::__construct((array) $data, 'http_debug');
    }

    public function __toString()
    {
        return \implode(\PHP_EOL, $this->content);
    }

    public function getIterator()
    {
        return $this->content;
    }

}
