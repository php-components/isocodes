<?php
/**
 * ISO 639-5
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
namespace ISOCodes\ISO639_5\Adapter;

use ISOCodes\Adapter\AbstractAdapter;
use ISOCodes\Exception;
use ISOCodes\ISO639_5\Model\ISO639_5;
use ISOCodes\ISO639_5\Model\ISO639_5Interface;

class Json extends AbstractAdapter implements AdapterInterface
{
    /**
     * @var array
     */
    protected $data;
    
    /**
     * Get an object by its code.
     * 
     * @param string $code
     * @return ISO639_5Interface
     * @throws Exception\InvalidArgumentException
     */
    public function get($code)
    {
        if (null === $this->data) {
            $this->loadFile();
        }
        
        // Detect code
        if (preg_match('/^[a-zA-Z]{3}$/', $code)) {
            return (isset($this->data[strtoupper($code)]) ? $this->data[strtoupper($code)] : null);
        } else {
            throw new Exception\InvalidArgumentException('code must be a valid alpha-3 code.');
        }
        
        return null;
    }
    
    /**
     * Get all the objects.
     * 
     * @return ISO639_5Interface[]
     */
    public function getAll()
    {
        if (null === $this->data) {
            $this->loadFile();
        }
        
        return $this->data;
    }
    
    /**
     * Check if an object with the given code exists.
     * 
     * @param string|int $code
     * @return bool
     * @throws Exception\InvalidArgumentException
     */
    public function has($code)
    {
        if (null === $this->data) {
            $this->loadFile();
        }
        
        // Detect code
        if (preg_match('/^[a-zA-Z]{3}$/', $code)) {
            return isset($this->data[strtoupper($code)]);
        } else {
            throw new Exception\InvalidArgumentException('code must be a valid alpha-3 code.');
        }
        
        return false;
    }
    
    /**
     * Load the JSON file contents
     */
    protected function loadFile()
    {
        $filename = dirname(dirname(dirname(__DIR__))) . '/data/iso_639-5.json';
        
        if (!(file_exists($filename) && is_readable($filename))) {
            throw new Exception\FileNotFoundException(sprintf('%s not found or not readable.', $filename));
        }
        
        $data = json_decode(file_get_contents($filename), true);
        if (!is_array($data)) {
            throw new Exception\RuntimeException(sprintf('%s is not a valid JSON file.', $filename));
        }
        
        if (!array_key_exists('639-5' , $data)) {
            throw new Exception\RuntimeException(sprintf('%s is not a valid JSON file for ISO-639-5.', $filename));
        }
        
        $data = $data['639-5'];
        
        // Lazy load the protoype
        if (null === $this->modelPrototype) {
            $this->modelPrototype = new ISO639_5();
        } elseif (!$this->modelPrototype instanceof ISO639_5Interface) {
            throw new Exception\RuntimeException(sprintf('The model prototype for %s must be an instance of %s', __CLASS__, ISO639_5Interface::class));
        }
        
        // Setting objects and the primary key
        foreach ($data as $current) {
            $obj = clone $this->modelPrototype;
            $obj->exchangeArray($current);
            $obj->_translator = $this->getTranslator();
            
            $this->data[strtoupper($current['alpha_3'])] = $obj; 
        }
    }
}
