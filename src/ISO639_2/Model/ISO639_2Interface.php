<?php
/**
 * ISO 639-2
 * 
 * ISO 639-2 language codes
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
namespace ISOCodes\ISO639_2\Model;

interface ISO639_2Interface
{
    /**
     * Get three letter terminology code of the language
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
     * Get two letter alphabetic code of the language from part 1 (optional)
     * 
     * @return string
     */
    public function getAlpha2();

    /**
     * Get three letter bibliographic code of the language (optional)
     * 
     * @return string
     */
    public function getBibliographic();

    /**
     * Get common name of the language (optional)
     * 
     * @return string
     */
    public function getCommonName($locale = null);

}