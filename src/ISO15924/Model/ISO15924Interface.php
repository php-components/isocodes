<?php
/**
 * ISO 15924
 * 
 * Codes for the representation of names of scripts
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
namespace ISOCodes\ISO15924\Model;

interface ISO15924Interface
{
    /**
     * Get four letter alphabetic code of the script
     * 
     * @return string
     */
    public function getAlpha4();

    /**
     * Get name of the script
     * 
     * @return string
     */
    public function getName($locale = null);

    /**
     * Get three digit numeric code of the script, including leading zeros
     * 
     * @return string
     */
    public function getNumeric();

}