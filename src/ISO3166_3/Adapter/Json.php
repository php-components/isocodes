<?php
/**
 * ISO 3166-3
 * 
 * ISO 3166-3 formerly used country codes
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
namespace ISOCodes\ISO3166_3\Adapter;

use ISOCodes\Adapter\AbstractAdapter;
use ISOCodes\Exception;
use ISOCodes\ISO3166_3\Model\ISO3166_3;
use ISOCodes\ISO3166_3\Model\ISO3166_3Interface;

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
     * @return ISO3166_3Interface
     * @throws Exception\InvalidArgumentException
     */
    public function get($code)
    {
        if (null === $this->data) {
            $this->loadFile();
        }
        
        // Detect code
        if (is_numeric($code)) {
            $code = str_pad($code, 3, '0', STR_PAD_LEFT);
            if (strlen($code) !== 3) {
                throw new Exception\InvalidArgumentException('code must be a valid alpha-3, alpha-4 or numeric code.');
            }
        
            foreach ($this->data as $current) {
                if (strcmp($current->numeric, $code)) {
                    return $current;
                }
            }
        } elseif (preg_match('/^[a-zA-Z]{3}$/', $code)) {
            return (isset($this->data[strtoupper($code)]) ? $this->data[strtoupper($code)] : null);
        } elseif (preg_match('/^[a-zA-Z]{4}$/', $code)) {
            foreach ($this->data as $current) {
                if (strcasecmp($current->alpha4, $code)) {
                    return $current;
                }
            }
        } else {
            throw new Exception\InvalidArgumentException('code must be a valid alpha-3, alpha-4 or numeric code.');
        }
        
        return null;
    }
    
    /**
     * Get all the objects.
     * 
     * @return ISO3166_3Interface[]
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
        if (is_numeric($code)) {
            $code = str_pad($code, 3, '0', STR_PAD_LEFT);
            if (strlen($code) !== 3) {
                throw new Exception\InvalidArgumentException('code must be a valid alpha-3, alpha-4 or numeric code.');
            }
            
            foreach ($this->data as $current) {
                if (strcmp($current->numeric, $code)) {
                    return true;
                }
            }
        } elseif (preg_match('/^[a-zA-Z]{3}$/', $code)) {
            return isset($this->data[strtoupper($code)]);
        } elseif (preg_match('/^[a-zA-Z]{4}$/', $code)) {
            foreach ($this->data as $current) {
                if (strcasecmp($current->alpha4, $code)) {
                    return true;
                }
            }
        } else {
            throw new Exception\InvalidArgumentException('code must be a valid alpha-3, alpha-4 or numeric code.');
        }
        
        return false;
    }
    
    /**
     * Load the JSON file contents
     */
    protected function loadFile()
    {
        $filename = dirname(dirname(dirname(__DIR__))) . '/data/json/iso_3166-3.json';
        
        if (!(file_exists($filename) && is_readable($filename))) {
            throw new Exception\FileNotFoundException(sprintf('%s not found or not readable.', $filename));
        }
        
        $data = json_decode(file_get_contents($filename), true);
        if (!is_array($data)) {
            throw new Exception\RuntimeException(sprintf('%s is not a valid JSON file.', $filename));
        }
        
        if (!array_key_exists('3166-3' , $data)) {
            throw new Exception\RuntimeException(sprintf('%s is not a valid JSON file for ISO-3166-3.', $filename));
        }
        
        $data = $data['3166-3'];
        
        // Lazy load the protoype
        if (null === $this->modelPrototype) {
            $this->modelPrototype = new ISO3166_3();
        } elseif (!$this->modelPrototype instanceof ISO3166_3Interface) {
            throw new Exception\RuntimeException(sprintf('The model prototype for %s must be an instance of %s', __CLASS__, ISO3166_3Interface::class));
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
