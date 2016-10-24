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
namespace ISOCodes\ISO4217\Adapter;

use ISOCodes\Exception;
use ISOCodes\ISO4217\Model\ISO4217Interface;
use ISOCodes\ISO4217\Model\ISO4217;
use ISOCodes\Adapter\AbstractPdoAdapter;

class Pdo extends AbstractPdoAdapter implements AdapterInterface
{    
    /**
     * Get an object by its code.
     *
     * @param string $code
     * @return ISO4217Interface
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
     * @return ISO4217Interface[]
     */
    public function getAll()
    {
        $data      = array();
        $prototype = $this->getObjectPrototype();
    
        $result = $this->pdo->query("SELECT * FROM iso_4217");
    
        foreach($result as $row) {
            $obj = clone $prototype;
            $obj->exchangeArray($row);
            $obj->_translator = $this->getTranslator();
    
            $data[] = $obj;
        }
    
        return $data;
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
        if (is_numeric($code)) {
            $code = str_pad($code, 3, '0', STR_PAD_LEFT);
            if (strlen($code) !== 3) {
                throw new Exception\InvalidArgumentException('code must be a valid alpha-3 or numeric code.');
            }
        
            $where              .= 'numeric = :numeric';
            $params[':numeric']  = $code;
        } elseif (preg_match('/^[a-zA-Z]{3}$/', $code)) {
            $where              .= 'alpha_3 = :alpha_3';
            $params[':alpha_3']  = strtoupper($code);
        } else {
            throw new Exception\InvalidArgumentException('code must be a valid alpha-3 or numeric code.');
        }
    
        $statement = $this->pdo->prepare('SELECT * FROM iso_4217 WHERE ' . $where);
        $result    = $statement->execute($params);
        if (!$result) {
            return false;
        }
    
        return $statement->fetch(\PDO::FETCH_ASSOC);
    }
    
    protected function getObjectPrototype()
    {
        if (null === $this->modelPrototype) {
            $this->modelPrototype = new ISO4217();
        } elseif (!$this->modelPrototype instanceof ISO4217Interface) {
            throw new Exception\RuntimeException(sprintf('The model prototype for %s must be an instance of %s', __CLASS__, ISO4217Interface::class));
        }
    
        return $this->modelPrototype;
    }
}
