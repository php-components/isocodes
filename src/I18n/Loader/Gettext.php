<?php
/**
 * ISO Codes
 * 
 * This code is a stripped down version of Zend Framewok loader.
 * 
 * Copyright © 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
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
namespace ISOCodes\I18n\Loader;

use ISOCodes\I18n\Exception;

class Gettext
{
    /**
     * Current file pointer.
     *
     * @var resource
     */
    protected $file;
    
    /**
     * Whether the current file is little endian.
     *
     * @var bool
     */
    protected $littleEndian;
    
    /**
     * Load translations from a file.
     *
     * @param  string $locale
     * @param  string $filename
     * @return TextDomain
     * @throws Exception\InvalidArgumentException()
     */
    public function load($locale, $filename)
    {
        if ((!file_exists($filename)) || (!is_readable($filename))) {
            throw new Exception\InvalidArgumentException(sprintf(
                'Could not find or open file %s for reading',
                $filename
            ));
        }
        
        $textDomain = new \ArrayObject([]);
        
        $this->file = fopen($filename, 'rb');
        
        if (false === $this->file) {
            throw new Exception\InvalidArgumentException(sprintf(
                'Could not open file %s for reading',
                $filename
            ));
        }
        
        // Verify magic number
        $magic = fread($this->file, 4);
        
        if ($magic == "\x95\x04\x12\xde") {
            $this->littleEndian = false;
        } elseif ($magic == "\xde\x12\x04\x95") {
            $this->littleEndian = true;
        } else {
            fclose($this->file);
            throw new Exception\InvalidArgumentException(sprintf(
                '%s is not a valid gettext file',
                $filename
            ));
        }
        
        // Verify major revision (only 0 and 1 supported)
        $majorRevision = ($this->readInteger() >> 16);
        
        if ($majorRevision !== 0 && $majorRevision !== 1) {
            fclose($this->file);
            throw new Exception\InvalidArgumentException(sprintf(
                '%s has an unknown major revision',
                $filename
            ));
        }
        
        // Gather main information
        $numStrings                   = $this->readInteger();
        $originalStringTableOffset    = $this->readInteger();
        $translationStringTableOffset = $this->readInteger();
        
        // Usually there follow size and offset of the hash table, but we have
        // no need for it, so we skip them.
        fseek($this->file, $originalStringTableOffset);
        $originalStringTable = $this->readIntegerList(2 * $numStrings);
        
        fseek($this->file, $translationStringTableOffset);
        $translationStringTable = $this->readIntegerList(2 * $numStrings);
        
        // Read in all translations
        for ($current = 0; $current < $numStrings; $current++) {
            $sizeKey                 = $current * 2 + 1;
            $offsetKey               = $current * 2 + 2;
            $originalStringSize      = $originalStringTable[$sizeKey];
            $originalStringOffset    = $originalStringTable[$offsetKey];
            $translationStringSize   = $translationStringTable[$sizeKey];
            $translationStringOffset = $translationStringTable[$offsetKey];
        
            $originalString = [''];
            if ($originalStringSize > 0) {
                fseek($this->file, $originalStringOffset);
                $originalString = explode("\0", fread($this->file, $originalStringSize));
            }
        
            if ($translationStringSize > 0) {
                fseek($this->file, $translationStringOffset);
                $translationString = explode("\0", fread($this->file, $translationStringSize));
        
                if (count($originalString) > 1 && count($translationString) > 1) {
                    $textDomain[$originalString[0]] = $translationString;
        
                    array_shift($originalString);
        
                    foreach ($originalString as $string) {
                        if (! isset($textDomain[$string])) {
                            $textDomain[$string] = '';
                        }
                    }
                } else {
                    $textDomain[$originalString[0]] = $translationString[0];
                }
            }
        }
        
        // Read header entries
        if (property_exists($textDomain, '')) {
            //  We really don't need this for our needs
            //$rawHeaders = explode("\n", trim($textDomain['']));
            //
            //foreach ($rawHeaders as $rawHeader) {
            //    list($header, $content) = explode(':', $rawHeader, 2);
            //
            //    if (trim(strtolower($header)) === 'plural-forms') {
            //        $textDomain->setPluralRule(PluralRule::fromString($content));
            //   }
            //}
        
            unset($textDomain['']);
        }
        
        fclose($this->file);
        
        return $textDomain;
    }
    
    /**
     * Read a single integer from the current file.
     *
     * @return int
     */
    protected function readInteger()
    {
        if ($this->littleEndian) {
            $result = unpack('Vint', fread($this->file, 4));
        } else {
            $result = unpack('Nint', fread($this->file, 4));
        }
    
        return $result['int'];
    }
    
    /**
     * Read an integer from the current file.
     *
     * @param  int $num
     * @return int
     */
    protected function readIntegerList($num)
    {
        if ($this->littleEndian) {
            return unpack('V' . $num, fread($this->file, 4 * $num));
        }
    
        return unpack('N' . $num, fread($this->file, 4 * $num));
    }
}
