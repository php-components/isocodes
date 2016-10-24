<?php
/**
 * ISO 3166-2
 * 
 * ISO 3166-2 country and subdivision codes
 * 
* Copyright (c) 2016 Juan Pedro Gonzalez Gutierrez
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
namespace ISOCodes\ISO3166_2\Model;

interface ISO3166_2Interface
{
    /**
     * Get code of the country subset item
     * 
     * @return string
     */
    public function getCode();

    /**
     * Get name of the country subset item
     * 
     * @return string
     */
    public function getName($locale = null);

    /**
     * Get parent of the country subset item (optional)
     * 
     * @return string
     */
    public function getParent();

    /**
     * Get type of subset of the country
     * 
     * @return string
     */
    public function getType($locale = null);

}