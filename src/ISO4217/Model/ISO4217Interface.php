<?php
/**
 * ISO 4217
 * 
 * ISO 4217 currencies
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
namespace ISOCodes\ISO4217\Model;

interface ISO4217Interface
{
    /**
     * Get three letter code of the currency
     * 
     * @return string
     */
    public function getAlpha3();

    /**
     * Get name of currency
     * 
     * @return string
     */
    public function getName($locale = null);

    /**
     * Get three digit numeric code of the item, including leading zeros
     * 
     * @return string
     */
    public function getNumeric();

}