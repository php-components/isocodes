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

use ISOCodes\Exception;
use ISOCodes\ISO3166_2\Model\ISO3166_2Interface;
use ISOCodes\ISO3166_2\Model\ISO3166_2;
use ISOCodes\Adapter\AbstractPdoAdapter;

class Pdo extends AbstractPdoAdapter implements AdapterInterface
{
    /**
     * Get an object by its code.
     *
     * @param string $code
     * @return ISO3166_1Interface
     * @throws Exception\InvalidArgumentException
     */
    public function get($code)
    {
        $params    = array();
        $prototype = $this->getObjectPrototype();
    
        $statement = $this->pdo->prepare('SELECT * FROM iso_3166_2 WHERE code = :code');
        $result    = $statement->execute(array(':code' => $code));
        if (!$result) {
            return null;
        }
    
        $data = $statement->fetch(\PDO::FETCH_ASSOC);
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
     * @param $parent The parent code so we can retrieve all the childs.
     * @return ISO3166_2Interface[]
     */
    public function getAll($parent = null)
    {
        $data      = array();
        $prototype = $this->getObjectPrototype();
    
        if (empty($parent)) {
            $result = $this->pdo->query("SELECT * FROM iso_3166_2");
        } else {
            $parent = strtoupper($parent);
            if (preg_match('/^([A-Z]{2})-([A-Z0-9]+)$/', $parent, $matches)) {
                $code   = $matches[1];
                $parent = $matches[2];
                
                $statement = $this->pdo->prepare('SELECT * FROM iso_3166_2 WHERE (code LIKE :code) AND (parent = :parent);');
                $result    = $statement->execute(array(':code' => $code . '-%', ':parent'=> $parent));
            } elseif (preg_match('/^([A-Z]{2})$/', $parent, $matches)) {
                $statement = $this->pdo->prepare('SELECT * FROM iso_3166_2 WHERE (code LIKE :code) AND (parent IS NULL);');
                $result    = $statement->execute(array(':code' => $parent . '-%'));
            } else {
                throw new Exception\InvalidArgumentException('invalid parent code.');
            }
            
            if (!$result) {
                return array();
            }
            
            $result = $statement->fetchAll(\PDO::FETCH_ASSOC);
        }
        
        if (!$result) {
            return array();
        }
    
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
        $params    = array();
        $prototype = $this->getObjectPrototype();
    
        $statement = $this->pdo->prepare('SELECT * FROM iso_3166_2 WHERE code = :code');
        $result    = $statement->execute(array(':code' => $code));
        if (!$result) {
            return false;
        }
    
        $data = $statement->fetch(\PDO::FETCH_ASSOC);
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
                throw new Exception\InvalidArgumentException('code must be a valid alpha-2, alpha-3 or numeric code.');
            }
    
            $where              .= 'numeric = :numeric';
            $params[':numeric']  = $code;
        } elseif (preg_match('/^[a-zA-Z]{2}$/', $code)) {
            $where              .= 'alpha_2 = :alpha_2';
            $params[':alpha_2']  = strtoupper($code);
        } elseif (preg_match('/^[a-zA-Z]{3}$/', $code)) {
            $where              .= 'alpha_3 = :alpha_3';
            $params[':alpha_3']  = strtoupper($code);
        } else {
            throw new Exception\InvalidArgumentException('code must be a valid alpha-2, alpha-3 or numeric code.');
        }
    
        $statement = $this->pdo->prepare('SELECT * FROM iso_3166_2 WHERE ' . $where);
        $result    = $statement->execute($params);
        if (!$result) {
            return false;
        }
    
        return $statement->fetch(\PDO::FETCH_ASSOC);
    }
    
    protected function getObjectPrototype()
    {
        if (null === $this->modelPrototype) {
            $this->modelPrototype = new ISO3166_3();
        } elseif (!$this->modelPrototype instanceof ISO3166_3Interface) {
            throw new Exception\RuntimeException(sprintf('The model prototype for %s must be an instance of %s', __CLASS__, ISO3166_3Interface::class));
        }
    
        return $this->modelPrototype;
    }
}
