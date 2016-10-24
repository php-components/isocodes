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
namespace ISOCodes\ISO3166_2\Adapter;

use ISOCodes\Adapter\AbstractAdapter;
use ISOCodes\Exception;
use ISOCodes\ISO3166_2\Model\ISO3166_2Interface;
use ISOCodes\ISO3166_2\Model\ISO3166_2;

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
     * @return ISO3166_2Interface
     * @throws Exception\InvalidArgumentException
     */
    public function get($code)
    {
        if (null === $this->data) {
            $this->loadFile();
        }
        
        return (isset($this->data[strtoupper($code)]) ? $this->data[strtoupper($code)] : null);
    }
    
    /**
     * Get all the objects.
     * 
     * @param $parent The parent code so we can retrieve all the childs.
     * @return ISO3166_2Interface[]
     */
    public function getAll($parent = null)
    {
        if (null === $this->data) {
            $this->loadFile();
        }

        if ($parent === null) {
            return $this->data;
        }
        
        // create the results array
        $results = array();
        
        $parent = strtoupper($parent);
        if (preg_match('/^([A-Z]{2})-([A-Z0-9]+)$/', $parent, $matches)) {
            $code   = $matches[1];
            $parent = $matches[2];
        
            foreach ($this->data as $current) {
                if (strcasecmp($current->parent, $parent) === 0) {
                    if (preg_match('/^' . $code . '-([A-Z0-9]+)$/', $current->code)) {
                        $results[] = $current;
                    }
                }
            }
        } elseif (preg_match('/^([A-Z]{2})$/', $parent)) {
            foreach ($this->data as $current) {
                if (empty($current->parent)) {
                    if (preg_match('/^' . $parent . '-([A-Z0-9]+)$/', $current->code)) {
                        $results[] = $current;
                    }
                }
            }
        } else {
            throw new Exception\InvalidArgumentException('invalid parent code.');
        }
        
        return $results;
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
        
        return isset($this->data[strtoupper($code)]);
    }
    
    /**
     * Load the JSON file contents
     */
    protected function loadFile()
    {
        $filename = dirname(dirname(dirname(__DIR__))) . '/data/json/iso_3166-2.json';
        
        if (!(file_exists($filename) && is_readable($filename))) {
            throw new Exception\FileNotFoundException(sprintf('%s not found or not readable.', $filename));
        }
        
        $data = json_decode(file_get_contents($filename), true);
        if (!is_array($data)) {
            throw new Exception\RuntimeException(sprintf('%s is not a valid JSON file.', $filename));
        }
        
        if (!array_key_exists('3166-2' , $data)) {
            throw new Exception\RuntimeException(sprintf('%s is not a valid JSON file for ISO-3166-2.', $filename));
        }
        
        $data = $data['3166-2'];
        
        // Lazy load the protoype
        if (null === $this->modelPrototype) {
            $this->modelPrototype = new ISO3166_2();
        } elseif (!$this->modelPrototype instanceof ISO3166_2Interface) {
            throw new Exception\RuntimeException(sprintf('The model prototype for %s must be an instance of %s', __CLASS__, ISO3166_2Interface::class));
        }
        
        // Setting objects and the primary key
        foreach ($data as $current) {
            $obj = clone $this->modelPrototype;
            $obj->exchangeArray($current);
            $obj->_translator = $this->getTranslator();
            
            $this->data[strtoupper($current['code'])] = $obj; 
        }
    }
}
