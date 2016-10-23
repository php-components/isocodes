<?php
/**
 * ISO 15924
 *
 * Copyright © 2016 Juan Pedro Gonzalez Gutierrez
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301  USA
 */
namespace ISOCodes\ISO15924\Adapter;

use ISOCodes\Adapter\AdapterInterface as BaseAdapterInterface;
use ISOCodes\ISO15924\Model\ISO15924Interface;

interface AdapterInterface extends BaseAdapterInterface
{    
    /**
     * Get an object by its code.
     * 
     * @param string $code
     * @return ISO15924Interface
     * @throws Exception\InvalidArgumentException
     */
    public function get($code);
    
    /**
     * Get all the objects.
     * 
     * @return ISO15924Interface[];
     */
    public function getAll();

    /**
     * Check if an object with the given code exists.
     * 
     * @param string|int $code
     * @return bool
     */
    public function has($code);
}
