<?php
/**
 * ISO 639-3
 * 
 * ISO 639-3 language codes
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
namespace ISOCodes\ISO639_3\Model;

use ISOCodes\I18n\Translator;
use ISOCodes\ISO639_3\Exception;

class ISO639_3 implements ISO639_3Interface
{
    /**
     * The translator
     * 
     * @var Translator
     */
    protected $_translator;

    /**
     * Three letter terminology code of the language
     * 
     * @var string
     */
    protected $alpha3;

    /**
     * Reference name of the language
     * 
     * @var string
     */
    protected $name;

    /**
     * Scope of the language: I(ndividual), M(acrolanguage), S(pecial)
     * 
     * @var string
     */
    protected $scope;

    /**
     * Type of the language: A(ncient), C(onstructed), E(xtinct), H(istorical), L(iving), S(pecial)
     * 
     * @var string
     */
    protected $type;

    /**
     * Two letter alphabetic code of the language from part 1 (optional)
     * 
     * @var string
     */
    protected $alpha2;

    /**
     * Common name of the language (optional)
     * 
     * @var string
     */
    protected $commonName;

    /**
     * Inverted name of the language (optional)
     * 
     * @var string
     */
    protected $invertedName;

    /**
     * Three letter bibliographic code of the language from part 2 (optional)
     * 
     * @var string
     */
    protected $bibliographic;

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
            case 'alpha_3':
            case 'alpha3':
                return $this->alpha3;
                break;
            case 'name':
                return $this->name;
                break;
            case 'scope':
                return $this->scope;
                break;
            case 'type':
                return $this->type;
                break;
            case 'alpha_2':
            case 'alpha2':
                return $this->alpha2;
                break;
            case 'common_name':
            case 'commonName':
                return $this->commonName;
                break;
            case 'inverted_name':
            case 'invertedName':
                return $this->invertedName;
                break;
            case 'bibliographic':
                return $this->bibliographic;
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
            case 'alpha_3':
            case 'alpha3':
                return isset($this->alpha3);
                break;
            case 'name':
                return isset($this->name);
                break;
            case 'scope':
                return isset($this->scope);
                break;
            case 'type':
                return isset($this->type);
                break;
            case 'alpha_2':
            case 'alpha2':
                return isset($this->alpha2);
                break;
            case 'common_name':
            case 'commonName':
                return isset($this->commonName);
                break;
            case 'inverted_name':
            case 'invertedName':
                return isset($this->invertedName);
                break;
            case 'bibliographic':
                return isset($this->bibliographic);
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
            case 'alpha_3':
            case 'alpha3':
            case 'name':
            case 'scope':
            case 'type':
            case 'alpha_2':
            case 'alpha2':
            case 'common_name':
            case 'commonName':
            case 'inverted_name':
            case 'invertedName':
            case 'bibliographic':
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

        return $this->_translator->translate($message, 'iso-639-3', $locale);
    }
    /**
     * Get three letter terminology code of the language
     * 
     * @return string
     */
    public function getAlpha3()
    {
        return $this->alpha3;
    }

    /**
     * Get reference name of the language
     * 
     * @return string
     */
    public function getName($locale = null)
    {
        return $this->_translate($this->name, $locale);
    }

    /**
     * Get scope of the language: I(ndividual), M(acrolanguage), S(pecial)
     * 
     * @return string
     */
    public function getScope()
    {
        return $this->scope;
    }

    /**
     * Get type of the language: A(ncient), C(onstructed), E(xtinct), H(istorical), L(iving), S(pecial)
     * 
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Get two letter alphabetic code of the language from part 1 (optional)
     * 
     * @return string
     */
    public function getAlpha2()
    {
        return $this->alpha2;
    }

    /**
     * Get common name of the language (optional)
     * 
     * @return string
     */
    public function getCommonName($locale = null)
    {
        return $this->_translate($this->commonName, $locale);
    }

    /**
     * Get inverted name of the language (optional)
     * 
     * @return string
     */
    public function getInvertedName($locale = null)
    {
        return $this->_translate($this->invertedName, $locale);
    }

    /**
     * Get three letter bibliographic code of the language from part 2 (optional)
     * 
     * @return string
     */
    public function getBibliographic()
    {
        return $this->bibliographic;
    }

    /**
     * Exchange the array with this object
     * 
     * @param array $input
     */
     public function exchangeArray($input)
     {
        // make sure we have all the data
        $input['alpha_3']       = isset($input['alpha_3'])       ? $input['alpha_3']       : null;
        $input['name']          = isset($input['name'])          ? $input['name']          : null;
        $input['scope']         = isset($input['scope'])         ? $input['scope']         : null;
        $input['type']          = isset($input['type'])          ? $input['type']          : null;
        $input['alpha_2']       = isset($input['alpha_2'])       ? $input['alpha_2']       : null;
        $input['common_name']   = isset($input['common_name'])   ? $input['common_name']   : null;
        $input['inverted_name'] = isset($input['inverted_name']) ? $input['inverted_name'] : null;
        $input['bibliographic'] = isset($input['bibliographic']) ? $input['bibliographic'] : null;

        if (empty($input['alpha_3'])) {
            throw new Exception\InvalidArgumentException('alpha_3 is a required property an can not be empty.');
        }
        if (!preg_match('/^[a-z]{3}$/', $input['alpha_3'])) {
            throw new Exception\InvalidArgumentException('alpha_3 has an invalid value.');
        }

        if (empty($input['name'])) {
            throw new Exception\InvalidArgumentException('name is a required property an can not be empty.');
        }

        if (empty($input['scope'])) {
            throw new Exception\InvalidArgumentException('scope is a required property an can not be empty.');
        }
        if (!preg_match('/^[IMS]$/', $input['scope'])) {
            throw new Exception\InvalidArgumentException('scope has an invalid value.');
        }

        if (empty($input['type'])) {
            throw new Exception\InvalidArgumentException('type is a required property an can not be empty.');
        }
        if (!preg_match('/^[ACEHLS]$/', $input['type'])) {
            throw new Exception\InvalidArgumentException('type has an invalid value.');
        }

        if ((!empty($input['alpha_2'])) && (!preg_match('/^[a-z]{2}$/', $input['alpha_2']))) {
            throw new Exception\InvalidArgumentException('alpha_2 has an invalid value.');
        }

        if ((!empty($input['bibliographic'])) && (!preg_match('/^[a-z]{3}$/', $input['bibliographic']))) {
            throw new Exception\InvalidArgumentException('bibliographic has an invalid value.');
        }

        $this->alpha3        = $input['alpha_3'];
        $this->name          = $input['name'];
        $this->scope         = $input['scope'];
        $this->type          = $input['type'];
        $this->alpha2        = $input['alpha_2'];
        $this->commonName    = $input['common_name'];
        $this->invertedName  = $input['inverted_name'];
        $this->bibliographic = $input['bibliographic'];
     }

    /**
     * Creates a copy of the object as an array.
     * 
     * @return array
     */
    public function getArrayCopy()
    {
        return array(
            'alpha_3'       => $this->alpha3,
            'name'          => $this->name,
            'scope'         => $this->scope,
            'type'          => $this->type,
            'alpha_2'       => $this->alpha2,
            'common_name'   => $this->commonName,
            'inverted_name' => $this->invertedName,
            'bibliographic' => $this->bibliographic,
        );
    }

}