<?php
/**
 * ISO Codes
 *
 * ISO Codes abstract adapter
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
namespace ISOCodes\Adapter;

use ISOCodes\I18n\Translator;
use ISOCodes\I18n\TranslatorInterface;

abstract class AbstractAdapter implements AdapterInterface
{
    /**
     * @var TranslatorInterface
     */
    protected $translator;
    
    /**
     * @var object
     */
    protected $modelPrototype;
    
    /**
     * Get the translator.
     *
     * @return TranslatorInterface
     */
    public function getTranslator()
    {
        if (!$this->translator instanceof TranslatorInterface) {
            $this->translator = new Translator();
        }
        
        return $this->translator;
    }
}