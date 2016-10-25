<?php
/**
 * ISO 639-2
 * 
 * ISO 639-2 language codes
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
namespace ISOCodes\ISO639_2\Adapter;

use ISOCodes\Exception;
use ISOCodes\ISO639_2\Model\ISO639_2;
use ISOCodes\ISO639_2\Model\ISO639_2Interface;
use ISOCodes\Adapter\AbstractPdoAdapter;

class Pdo extends AbstractPdoAdapter implements AdapterInterface
{
    /**
     * Get an object by its code.
     *
     * @param string $code
     * @return ISO639_2Interface
     * @throws Exception\InvalidArgumentException
     */
    public function get($code)
    {
        $prototype = $this->getObjectPrototype();
    
        $data = $this->fetchByCode($code);
        if (!$data) {
            return null;
        }
    
        $obj = clone $prototype;
        $obj->exchangeArray($data);
        $obj->_translator = $this->getTranslator();
    
        return $obj;
    }
    
    /**
     * Get all the objects.
     *
     * @return ISO639_2Interface[]
     */
    public function getAll()
    {
        $data      = array();
        $prototype = $this->getObjectPrototype();
    
        $result = $this->pdo->query("SELECT * FROM iso_639_2");
    
        foreach($result as $row) {
            $obj = clone $prototype;
            $obj->exchangeArray($row);
            $obj->_translator = $this->getTranslator();
    
            $data[] = $obj;
        }
    
        return $data;
    }
    
    /**
     * Get an object by its code.
     *
     * @param string $code
     * @return ISO639_2Interface
     * @throws Exception\InvalidArgumentException
     */
    public function getBibliographic($code)
    {
        $prototype = $this->getObjectPrototype();
    
        $data = $this->fetchByBibliographic($code);
        if (!$data) {
            return null;
        }
    
        $obj = clone $prototype;
        $obj->exchangeArray($data);
        $obj->_translator = $this->getTranslator();
    
        return $obj;
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
        $data = $this->fetchByCode($code);
        if (!$data) {
            return false;
        }
    
        return true;
    }
    
    /**
     * Check if an object with the given bibliographic code exists.
     * 
     * @param string|int $code
     * @return bool
     * @throws Exception\InvalidArgumentException
     */
    public function hasBibliographic($code)
    {
        $data = $this->fetchByBibliographic($code);
        if (!$data) {
            return false;
        }
    
        return true;
    }
    
    /**
     *
     * @param unknown $code
     * @throws Exception\InvalidArgumentException
     * @return mixed
     */
    protected function fetchByCode($code)
    {
        $where     = '';
        $params    = array();
        
        // Detect code
        if (preg_match('/^[a-zA-Z]{2}$/', $code)) {
            $where              .= 'alpha_2 = :alpha_2';
            $params[':alpha_2']  = strtolower($code);
        } elseif (preg_match('/^[a-zA-Z]{3}$/', $code)) {
            $where              .= 'alpha_3 = :alpha_3';
            $params[':alpha_3']  = strtolower($code);
        } else {
            throw new Exception\InvalidArgumentException('code must be a valid alpha-2 or alpha-3 code.');
        }
    
        $statement = $this->pdo->prepare('SELECT * FROM iso_639_2 WHERE ' . $where);
        $result    = $statement->execute($params);
        if (!$result) {
            return false;
        }
    
        return $statement->fetch(\PDO::FETCH_ASSOC);
    }
    
    /**
     *
     * @param unknown $code
     * @throws Exception\InvalidArgumentException
     * @return mixed
     */
    protected function fetchByBibliographic($code)
    {
        $where     = '';
        $params    = array();
    
        // Detect code
        if (preg_match('/^[a-zA-Z]{3}$/', $code)) {
            $where              .= 'bibliographic = :bibliographic';
            $params[':bibliographic']  = strtolower($code);
        } else {
            throw new Exception\InvalidArgumentException('bibliograhic code must be a 3 letter code.');
        }
    
        $statement = $this->pdo->prepare('SELECT * FROM iso_639_2 WHERE ' . $where);
        $result    = $statement->execute($params);
        if (!$result) {
            return false;
        }
    
        return $statement->fetch(\PDO::FETCH_ASSOC);
    }
    
    protected function getObjectPrototype()
    {
        if (null === $this->modelPrototype) {
            $this->modelPrototype = new ISO639_2();
        } elseif (!$this->modelPrototype instanceof ISO639_2Interface) {
            throw new Exception\RuntimeException(sprintf('The model prototype for %s must be an instance of %s', __CLASS__, ISO639_2Interface::class));
        }
    
        return $this->modelPrototype;
    }
}
