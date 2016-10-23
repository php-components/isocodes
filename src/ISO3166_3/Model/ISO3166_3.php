<?php
/**
 * ISO 3166-3
 * 
 * ISO 3166-3 formerly used country codes
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
namespace ISOCodes\ISO3166_3\Model;

use ISOCodes\I18n\Translator;
use ISOCodes\ISO3166_3\Exception;

class ISO3166_3 implements ISO3166_3Interface
{
    /**
     * The translator
     * 
     * @var Translator
     */
    protected $_translator;

    /**
     * Three letter alphabetic code of the item
     * 
     * @var string
     */
    protected $alpha3;

    /**
     * Four letter alphabetic code of the item
     * 
     * @var string
     */
    protected $alpha4;

    /**
     * Name of the item
     * 
     * @var string
     */
    protected $name;

    /**
     * Three digit numeric code of the item, including leading zeros (optional)
     * 
     * @var string
     */
    protected $numeric;

    /**
     * Comment for the item (optional)
     * 
     * @var string
     */
    protected $comment;

    /**
     * Date of withdrawal from ISO 3166-1 (optional)
     * 
     * @var string
     */
    protected $withdrawalDate;

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
            case 'alpha_4':
            case 'alpha4':
                return $this->alpha4;
                break;
            case 'name':
                return $this->name;
                break;
            case 'numeric':
                return $this->numeric;
                break;
            case 'comment':
                return $this->comment;
                break;
            case 'withdrawal_date':
            case 'withdrawalDate':
                return $this->withdrawalDate;
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
            case 'alpha_4':
            case 'alpha4':
                return isset($this->alpha4);
                break;
            case 'name':
                return isset($this->name);
                break;
            case 'numeric':
                return isset($this->numeric);
                break;
            case 'comment':
                return isset($this->comment);
                break;
            case 'withdrawal_date':
            case 'withdrawalDate':
                return isset($this->withdrawalDate);
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
            case 'alpha_4':
            case 'alpha4':
            case 'name':
            case 'numeric':
            case 'comment':
            case 'withdrawal_date':
            case 'withdrawalDate':
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

        return $this->_translator->translate($message, 'iso-3166-3', $locale);
    }
    /**
     * Get three letter alphabetic code of the item
     * 
     * @return string
     */
    public function getAlpha3()
    {
        return $this->alpha3;
    }

    /**
     * Get four letter alphabetic code of the item
     * 
     * @return string
     */
    public function getAlpha4()
    {
        return $this->alpha4;
    }

    /**
     * Get name of the item
     * 
     * @return string
     */
    public function getName($locale = null)
    {
        return $this->_translate($this->name, $locale);
    }

    /**
     * Get three digit numeric code of the item, including leading zeros (optional)
     * 
     * @return string
     */
    public function getNumeric()
    {
        return $this->numeric;
    }

    /**
     * Get comment for the item (optional)
     * 
     * @return string
     */
    public function getComment($locale = null)
    {
        return $this->_translate($this->comment, $locale);
    }

    /**
     * Get date of withdrawal from ISO 3166-1 (optional)
     * 
     * TODO: make it a \DateTime
     * 
     * @return string
     */
    public function getWithdrawalDate()
    {
        return $this->withdrawalDate;
    }

    /**
     * Exchange the array with this object
     * 
     * @param array $input
     */
     public function exchangeArray($input)
     {
        // make sure we have all the data
        $input['alpha_3']         = isset($input['alpha_3'])         ? $input['alpha_3']         : null;
        $input['alpha_4']         = isset($input['alpha_4'])         ? $input['alpha_4']         : null;
        $input['name']            = isset($input['name'])            ? $input['name']            : null;
        $input['numeric']         = isset($input['numeric'])         ? $input['numeric']         : null;
        $input['comment']         = isset($input['comment'])         ? $input['comment']         : null;
        $input['withdrawal_date'] = isset($input['withdrawal_date']) ? $input['withdrawal_date'] : null;

        if (empty($input['alpha_3'])) {
            throw new Exception\InvalidArgumentException('alpha_3 is a required property an can not be empty.');
        }
        if (!preg_match('/^[A-Z]{3}$/', $input['alpha_3'])) {
            throw new Exception\InvalidArgumentException('alpha_3 has an invalid value.');
        }

        if (empty($input['alpha_4'])) {
            throw new Exception\InvalidArgumentException('alpha_4 is a required property an can not be empty.');
        }
        if (!preg_match('/^[A-Z]{2,4}$/', $input['alpha_4'])) {
            throw new Exception\InvalidArgumentException('alpha_4 has an invalid value.');
        }

        if (empty($input['name'])) {
            throw new Exception\InvalidArgumentException('name is a required property an can not be empty.');
        }

        if ((!empty($input['numeric']) && (!preg_match('/^[0-9]{3}$/', $input['numeric'])))) {
            throw new Exception\InvalidArgumentException('numeric has an invalid value.');
        }

        if (!preg_match('/^[0-9]{4}(|-[0-9]{2}){2}$/', $input['withdrawal_date'])) {
            throw new Exception\InvalidArgumentException('withdrawal_date has an invalid value.');
        }

        $this->alpha3         = $input['alpha_3'];
        $this->alpha4         = $input['alpha_4'];
        $this->name           = $input['name'];
        $this->numeric        = $input['numeric'];
        $this->comment        = $input['comment'];
        $this->withdrawalDate = $input['withdrawal_date'];
     }

    /**
     * Creates a copy of the object as an array.
     * 
     * @return array
     */
    public function getArrayCopy()
    {
        return array(
            'alpha_3'         => $this->alpha3,
            'alpha_4'         => $this->alpha4,
            'name'            => $this->name,
            'numeric'         => $this->numeric,
            'comment'         => $this->comment,
            'withdrawal_date' => $this->withdrawalDate,
        );
    }

}