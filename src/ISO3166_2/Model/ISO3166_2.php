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
namespace ISOCodes\ISO3166_2\Model;

use ISOCodes\I18n\Translator;
use ISOCodes\ISO3166_2\Exception;

class ISO3166_2 implements ISO3166_2Interface
{
    /**
     * The translator
     * 
     * @var Translator
     */
    protected $_translator;

    /**
     * Code of the country subset item
     * 
     * @var string
     */
    protected $code;

    /**
     * Name of the country subset item
     * 
     * @var string
     */
    protected $name;

    /**
     * Parent of the country subset item (optional)
     * 
     * @var string
     */
    protected $parent;

    /**
     * Type of subset of the country
     * 
     * @var string
     */
    protected $type;

    /**
     * Magic method utilized for reading data from inaccessible properties.
     * 
     * @param string $name
     * @return mixed
     */
    public function __get($name)
    {
        switch($name)
        {
            case '_translator':
                return $this->_translator;
                break;
            case 'code':
                return $this->code;
                break;
            case 'name':
                return $this->name;
                break;
            case 'parent':
                return $this->parent;
                break;
            case 'type':
                return $this->type;
                break;
        }

        trigger_error(sprintf('Undefined property: %s::$%s', __CLASS__, $name), E_USER_NOTICE);
    }

    /**
     * Magic method triggered by calling isset() or empty() on inaccessible properties.
     * 
     * @param string $name
     * @return bool
     */
    public function __isset($name)
    {
        switch($name)
        {
            case '_translator':
                if  (isset($this->translator) && ($this->_translator instanceof Translator)) {
                    return true;
                }
                return false;
                break;
            case 'code':
                return isset($this->code);
                break;
            case 'name':
                return isset($this->name);
                break;
            case 'parent':
                return isset($this->parent);
                break;
            case 'type':
                return isset($this->type);
                break;
        }

        return false;
    }

    /**
     * Magic method runned when writing data to inaccessible properties.
     * 
     * @param string $name
     * @param mixed $value
     * @return void
     */
    public function __set($name, $value)
    {
        switch($name)
        {
            case '_translator':
                if (!$value instanceof Translator) {
                    throw new Exception\InvalidArgumentException(sprintf(
                        '%s::$%s must be of the type ISOCodes\I18n\Translator, %s given',
                        __CLASS__,
                        $name,
                        (is_object($value) ? get_class($value) : gettype($value))
                    ));
                }
                $this->_translator = $value;
                return;
                break;
            case 'code':
            case 'name':
            case 'parent':
            case 'type':
                trigger_error(sprintf('Cannot access protected property %s::$%s', __CLASS__, $name), E_USER_ERROR);
                return;
                break;
        }

        trigger_error(sprintf('Undefined property: %s::$%s', __CLASS__, $name), E_USER_NOTICE);
    }

    /**
     * Get a translation for a message from the translator.
     * 
     * TODO: The class generator cannot tell for sure which fields are
     *       translatable so you must hook them by hand or the translator
     *       will be of no use.
     * 
     * @param string      $message The message to translate
     * @param string|null $locale  The locale of the translation or null for the default locale.
     * @return string
     */
    protected function _translate($message, $locale = null)
    {
        if (!$this->_translator instanceof Translator) {
            return $message;
        }

        return $this->_translator->translate($message, 'iso-3166-2', $locale);
    }
    /**
     * Get code of the country subset item
     * 
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * Get name of the country subset item
     * 
     * @return string
     */
    public function getName($locale = null)
    {
        return $this->_translate($this->name, $locale);
    }

    /**
     * Get parent of the country subset item (optional)
     * 
     * @return string
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * Get type of subset of the country
     * 
     * @return string
     */
    public function getType($locale = null)
    {
        return $this->_translate($this->type, $locale);
    }

    /**
     * Exchange the array with this object
     * 
     * @param array $input
     */
     public function exchangeArray($input)
     {
        // make sure we have all the data
        $input['code']   = isset($input['code'])   ? $input['code']   : null;
        $input['name']   = isset($input['name'])   ? $input['name']   : null;
        $input['parent'] = isset($input['parent']) ? $input['parent'] : null;
        $input['type']   = isset($input['type'])   ? $input['type']   : null;

        if (!preg_match('/^[A-Z]{2}-[A-Z0-9]+$/', $input['code'])) {
            throw new Exception\InvalidArgumentException('code has an invalid value.');
        }

        $this->code   = $input['code'];
        $this->name   = $input['name'];
        $this->parent = $input['parent'];
        $this->type   = $input['type'];
     }

    /**
     * Creates a copy of the object as an array.
     * 
     * @return array
     */
    public function getArrayCopy()
    {
        return array(
            'code'   => $this->code,
            'name'   => $this->name,
            'parent' => $this->parent,
            'type'   => $this->type,
        );
    }

}