<?php
/**
 * ISO 3166-1
 * 
 * ISO 3166-1 country codes
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
namespace ISOCodes\ISO3166_1\Model;

interface ISO3166_1Interface
{
    /**
     * Get two letter alphabetic code of the item
     * 
     * @return string
     */
    public function getAlpha2();

    /**
     * Get three letter alphabetic code of the item
     * 
     * @return string
     */
    public function getAlpha3();

    /**
     * Get name of the item
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

    /**
     * Get official name of the item (optional)
     * 
     * @return string
     */
    public function getOfficialName($locale = null);

    /**
     * Get common name of the item (optional)
     * 
     * @return string
     */
    public function getCommonName($locale = null);

}